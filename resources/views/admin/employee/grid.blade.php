<div class="row">
    <div class="row staff-grid-row">
        @foreach($emp as $user)
        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
            <div class="profile-widget">
                <div class="profile-img">
                    <a href="#" class="avatar">
                        <img src="#" alt="{{ $user->name }}" />
                    </a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="material-icons">more_vert</i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-toggle="modal"
                            data-target="#edit_employee_{{ $user->id }}">
                            <i class="fa fa-pencil m-r-5"></i> Edit
                        </a>
                        <a class="dropdown-item" href="#" data-toggle="modal"
                            data-target="#delete_employee_{{ $user->id }}">
                            <i class="fa fa-trash-o m-r-5"></i> Delete
                        </a>
                    </div>
                </div>
                <h4 class="user-name m-t-10 mb-0 text-ellipsis">
                    <a href="#">{{ $user->name }}</a>
                </h4>
                <div class="small text-muted">{{ $user->position }}</div>
            </div>
        </div>

        @endforeach
    </div>
</div>
