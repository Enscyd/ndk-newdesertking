<h2>Add Spare Part</h2>

<form method="POST" action="{{ route('sparepart.store') }}">
    @csrf

    <input name="name" placeholder="Name"><br><br>

    <input name="part_number" placeholder="Part Number"><br><br>

    <select name="category_id">
        <option value="">Select Category</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
    </select><br><br>

    <input name="quantity" type="number" placeholder="Quantity"><br><br>

    <input name="price" type="number" step="0.01" placeholder="Price"><br><br>

    <button type="submit">Save</button>
</form>