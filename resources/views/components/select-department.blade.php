<select name="department" class="border rounded-xl p-2">
  <option value="">Seleccione departamento</option>
  @foreach($departments as $d)
    <option value="{{ $d }}" @selected($selected===$d)>{{ $d }}</option>
  @endforeach
</select>
