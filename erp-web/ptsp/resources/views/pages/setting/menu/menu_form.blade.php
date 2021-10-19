@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Menu</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('menu') }}">Menu</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{$menu_by_id != null ? 'Edit' : 'Add'}}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection


@section('content')
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">
                                @if($menu_by_id != null)
                                Edit New Menu
                                @else
                                Add New Menu
                                @endif
                                </h4>
                                <form method="POST" action="{{ $menu_by_id != null ? URL::to('menu/edit/'.$menu_by_id->id) : route('menu/add') }}" class="needs-validation" novalidate>
                                  @csrf
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label for="validationTooltip01">Title</label>
                                      <input name="title" type="text" class="form-control" id="validationTooltip01" value="{{ $menu_by_id != null ? $menu_by_id->title : '' }}" placeholder="Title" required>
                                      <div class="invalid-tooltip">
                                          Please fill out this field.
                                      </div>
                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label for="validationTooltip01">Url</label>
                                      <input name="url" type="text" class="form-control" id="validationTooltip01" value="{{ $menu_by_id != null ? $menu_by_id->url : '' }}" placeholder="Url" required>
                                      <div class="invalid-tooltip">
                                          Please fill out this field.
                                      </div>
                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label for="validationTooltip01">Icon</label>
                                      <input name="icon" type="text" class="form-control" id="validationTooltip01" value="{{ $menu_by_id != null ? $menu_by_id->icon : '' }}" placeholder="Icon" required>
                                      <div class="invalid-tooltip">
                                          Please fill out this field.
                                      </div>
                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label for="validationTooltip01">Sequence Number</label>
                                      <input name="seq_no" type="number" class="form-control" id="validationTooltip01" value="{{ $menu_by_id != null ? $menu_by_id->seq_no : '' }}" placeholder="Sequence Number" value="0" required>
                                      <div class="invalid-tooltip">
                                          Please fill out this field.
                                      </div>
                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label for="validationTooltip01">Parent Menu</label>
                                      <!-- <input type="text" class="form-control" id="validationTooltip01" placeholder="Is Main Menu" required> -->
                                      <select name="is_main_menu" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                            <option value="">Select Main Menu</option>
                                            <option value="0" {{ $menu_by_id != null ? ($menu_by_id->is_main_menu == 0 ? 'selected' : '') : '' }}>Header Menu / Main Menu</option>
                                            @foreach($menus as $menu)
                                            <option value="{{ $menu->id }}" {{ $menu_by_id != null ? ($menu_by_id->is_main_menu == $menu->id ? 'selected' : '') : '' }}>{{ $menu->title }}</option>
                                            @endforeach
                                      </select>
                                      <div class="invalid-tooltip">
                                          Please select out this field.
                                      </div>
                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label for="validationTooltip01">Is Active</label>
                                      <select name="is_active" required class="form-control select2 custom-select"  style="width: 100%; height:32px;">
                                            <option value="1" {{ $menu_by_id != null ? ($menu_by_id->is_active == 1 ? 'selected' : '') : '' }}>Active</option>
                                            <option value="0" {{ $menu_by_id != null ? ($menu_by_id->is_active == 0 ? 'selected' : '') : '' }}>InActive</option>
                                      </select>
                                      <div class="invalid-tooltip">
                                          Please fill out this field.
                                      </div>
                                    </div>
                                  </div>
                                  <button class="btn btn-primary mt-4" type="submit">Submit</button>
                                </form>
                            </div>
                        </div>
                    
                    </div>
                </div>
            </div>
    

@endsection