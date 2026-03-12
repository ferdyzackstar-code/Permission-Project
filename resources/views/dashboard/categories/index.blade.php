@extends('dashboard.layouts.admin')

@section('content')
    @if ($message = Session::get('success'))
        <div class="alert alert-success mx-4 mt-3">
            <p class="mb-0">{{ $message }}</p>
        </div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Manajemen Kategori Anda Petshop</h1>

        @can('category-create')
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('dashboard.categories.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="name" class="form-control" placeholder="Nama Kategori..."
                                    required>
                            </div>
                            <div class="col-md-4">
                                <select name="parent_id" class="form-control">
                                    <option value="">-- Jadikan Kategori Utama --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Tambah Kategori</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kategori Utama</th>
                            <th>Sub-Kategori</th>
                            @can('category-edit', 'category-delete')
                                <th>Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>
                                    @foreach ($category->children as $child)
                                        <div class="btn-group mb-1">
                                            <span class="badge badge-info">{{ $child->name }}</span>
                                            @can('category-edit')
                                            <button class="btn btn-sm btn-light text-primary ml-1" data-toggle="modal"
                                                data-target="#editModal{{ $child->id }}">
                                                <i class="fas fa-edit fa-xs"></i>
                                            </button>
                                            @endcan
                                            <form action="{{ route('dashboard.categories.destroy', $child->id) }}"
                                                method="POST" style="display:inline">
                                                @csrf @method('DELETE')
                                                @can('category-delete')
                                                <button type="submit" class="btn btn-sm btn-light text-danger mr-4"
                                                    onclick="return confirm('Hapus sub-kategori ini?')">
                                                    <i class="fas fa-times fa-xs"></i>
                                                </button>
                                                @endcan
                                            </form>
                                        </div>
                                        @can('category-edit')
                                            @include('dashboard.categories.edit_modal', ['item' => $child])
                                        @endcan
                                    @endforeach
                                </td>
                                @can('category-edit', 'category-delete')
                                    <td>
                                        <div class="btn-group">
                                            @can('category-edit')
                                                <button class="btn btn-primary btn-sm mr-2" data-toggle="modal"
                                                    data-target="#editModal{{ $category->id }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            @endcan
                                            @can('category-delete')
                                                <form action="{{ route('dashboard.categories.destroy', $category->id) }}"
                                                    method="POST" style="display:inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Hapus kategori ini?')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                        @can('category-edit')
                                            @include('dashboard.categories.edit_modal', [
                                                'item' => $category,
                                            ])
                                        @endcan
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
