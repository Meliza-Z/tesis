<form action="{{ route('reporte.diario') }}" method="GET">
    <label for="fecha">Selecciona la fecha:</label>
    <input type="date" name="fecha" required>
    <button type="submit">Generar PDF</button>
</form>
