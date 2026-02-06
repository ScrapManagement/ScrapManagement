@extends('DashBoard.layout.main')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Create New Order</h4>
        
        <form action="{{ route('order.store') }}" method="POST" x-data="{ 
            rows: [{id: 1}] 
        }">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="60%">Product</th>
                            <th width="20%">Quantity</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in rows" :key="index">
                            <tr>
                                <td>
                                    <select :name="'products[' + index + '][id]'" class="form-control" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name }} ({{ $product->price }} EGP)
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" :name="'products[' + index + '][quantity]'" class="form-control" value="1" min="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" @click="rows.splice(index, 1)" x-show="rows.length > 1">X</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-2">
                <button type="button" class="btn btn-info btn-sm" @click="rows.push({id: Date.now()})">Add Another Product</button>
            </div>

            <div class="mt-4 border-top pt-3">
                <button type="submit" class="btn btn-primary">Place Order</button>
                <a href="{{ route('order.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection