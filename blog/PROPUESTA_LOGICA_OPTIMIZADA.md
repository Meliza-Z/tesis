# Propuesta Optimizada: Créditos con Mínima Redundancia

Esta propuesta elimina redundancias entre `creditos` y `detalle_creditos`, adopta enteros para montos (sin decimales) y define al menos 5 tipos de reportes.

## Principios
- Sin decimales en finanzas: usar enteros en la menor unidad (centavos). Ej.: `precio_unitario_centavos INT` en vez de `DECIMAL`.
- Totales derivados, no persistidos: calcular `monto_total` y `saldo_pendiente` a partir de `detalle_creditos` y `pagos`.
- Máximo 2 créditos abiertos por cliente; veto si existe alguno vencido.
- Plazo por defecto 15 días; se puede extender por fecha.

## Esquema propuesto (núcleo)
- clientes
  - nombre, cedula (única), direccion, telefono, email
  - limite_credito_centavos INT
- creditos (solo metadatos; sin totales persistidos)
  - cliente_id, codigo (único)
  - fecha_credito, plazo_dias INT DEFAULT 15
  - fecha_vencimiento DATE, fecha_vencimiento_ext DATE NULL
  - estado ENUM('pendiente','activo','vencido','pagado')
- detalle_creditos
  - credito_id, producto_id
  - cantidad INT
  - precio_unitario_centavos INT
  - subtotal_centavos INT (validar = cantidad * precio)
  - observaciones TEXT NULL
- productos
  - nombre, descripcion, categoria, precio_base_centavos INT
- pagos
  - credito_id
  - fecha_pago DATE
  - monto_pagado_centavos INT
  - metodo_pago VARCHAR(NULLABLE)
- cuentas_por_cobrar (agregado por cliente)
  - cliente_id, monto_adeudado_centavos INT, saldo_pendiente_centavos INT
  - fecha_vencimiento DATE, estado ENUM('al_dia','mora')
  - proximo_recordatorio_at DATETIME NULL

## Cálculos derivados (no guardar en `creditos`)
- monto_total_centavos(credito) = Σ `detalle_creditos.subtotal_centavos` del crédito.
- total_pagado_centavos(credito) = Σ `pagos.monto_pagado_centavos` del crédito.
- saldo_pendiente_centavos(credito) = monto_total - total_pagado.
- vencimiento_efectivo = COALESCE(fecha_vencimiento_ext, fecha_vencimiento).
- estado:
  - 'pagado' si saldo = 0.
  - 'vencido' si saldo > 0 y vencimiento_efectivo < hoy.
  - 'activo' si saldo > 0 y no vencido.

Sugerencia de vista SQL:
```sql
CREATE OR REPLACE VIEW vw_credito_saldos AS
SELECT c.id AS credito_id,
       SUM(d.subtotal_centavos) AS monto_total_centavos,
       COALESCE(
         (SELECT SUM(p.monto_pagado_centavos) FROM pagos p WHERE p.credito_id=c.id),0
       ) AS total_pagado_centavos,
       SUM(d.subtotal_centavos) - COALESCE((SELECT SUM(p.monto_pagado_centavos) FROM pagos p WHERE p.credito_id=c.id),0) AS saldo_pendiente_centavos
FROM creditos c
JOIN detalle_creditos d ON d.credito_id=c.id
GROUP BY c.id;
```

## Reglas de negocio (aplicación)
1) No permitir nuevos créditos si el cliente tiene ≥ 1 crédito vencido.
2) No permitir más de 2 créditos abiertos (activo/pendiente) por cliente.
3) Límite de exposición: (suma de saldos abiertos del cliente) + (monto_total del nuevo crédito) ≤ `limite_credito_centavos`.
4) Validar `subtotal_centavos = cantidad * precio_unitario_centavos`.
5) Pagos descuentan del saldo; al llegar a 0 cambiar estado a 'pagado'.

## Reportes (mínimo 5, aquí 6)
- Créditos vencidos: por cliente y días de mora + saldos.
- Cartera total: suma de `saldo_pendiente_centavos` y desglose por estado.
- Cobros del período: pagos por rango de fechas y método.
- Próximos a vencer: créditos que vencen en X días (ej. 3/7).
- Clientes en riesgo: cerca del límite de crédito o con 2 créditos abiertos.
- Ventas a crédito por producto/categoría: top productos y montos.

## Notificaciones y recordatorios
- Cuentas por cobrar muestra cuentas a vencer.
- Recordatorios automáticos vía WhatsApp antes del vencimiento y en mora, registrando `proximo_recordatorio_at`.

## Índices recomendados
- `creditos(cliente_id, estado, fecha_vencimiento)`
- `detalle_creditos(credito_id)`, `pagos(credito_id, fecha_pago)`
- `cuentas_por_cobrar(cliente_id)`

Notas: mantener cálculos en servicios/queries o vistas; evitar triggers complejos. Si se requiere rendimiento, considerar una tabla de saldos materializada actualizada por jobs.

