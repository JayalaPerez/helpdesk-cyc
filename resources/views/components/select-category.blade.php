<select name="category" class="border rounded-xl p-2">
  <option value="">Seleccione categoría</option>
  @foreach($categories as $c)
    <option value="{{ $c }}" @selected($selected===$c)>{{ $c }}</option>
  @endforeach
</select>
