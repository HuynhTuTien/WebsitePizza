{{-- resources/views/admin/ingredient/edit.blade.php --}}
@extends('layouts.admin')

@section('content')

<div class="content-body">
    <div class="container w-50">
        <div class="col-xl-12">
            <div class="card dz-card" id="bootstrap-table1">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="Preview" role="tabpanel" aria-labelledby="home-tab">
                        <div class="card-header flex-wrap border-0">
                            <div>
                                <h2 class="card-title">Sửa thông tin nguyên liệu</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <form action="{{ route('ingredient.update', $ingredient->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên nguyên liệu</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $ingredient->name) }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="unit" class="form-label">Đơn vị tính</label>
                                    <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit', $ingredient->unit) }}">
                                    @error('unit')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Cập nhật</button>

                                <a href="{{ route('ingredient.list') }}" class="btn btn-primary mt-2 me-1 m-2 float-end">Quay lại</a>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
