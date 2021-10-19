        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <!-- User Profile-->
                        <li>
                            <!-- User Profile-->
                            <div class="user-profile d-flex no-block dropdown mt-3">
                                <div class="user-pic"><img src="{!! asset('theme/assets/images/users/1.jpg') !!}" alt="users" class="rounded-circle" width="40" /></div>
                                <div class="user-content hide-menu ml-2">
                                    <a href="javascript:void(0)" class="" id="Userdd" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <h5 class="mb-0 user-name font-medium">{{ Auth::user()->name }} <i class="fa fa-angle-down"></i></h5>
                                        <span class="op-5 user-email">{{ Auth::user()->email }}</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="Userdd">
                                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-user mr-1 ml-1"></i> My Profile</a>
                                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-wallet mr-1 ml-1"></i> My Balance</a>
                                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-email mr-1 ml-1"></i> Inbox</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-settings mr-1 ml-1"></i> Account Setting</a>
                                        <div class="dropdown-divider"></div>
                                        <!-- <a class="dropdown-item" href="javascript:void(0)"><i class="fa fa-power-off mr-1 ml-1"></i> Logout</a> -->
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();">
                                            <i class="fa fa-power-off mr-1 ml-1"></i> Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End User Profile-->
                        </li>
                        <!-- <li class="p-15 mt-2"><a href="javascript:void(0)" class="btn btn-block create-btn text-white no-block d-flex align-items-center"><i class="fa fa-plus-square"></i> <span class="hide-menu ml-1">Create New</span> </a></li> -->
                        <!-- User Profile-->

                        <?php
                        $menu_permission = DB::table('user_permission')
                                            ->where('role_id', Auth::user()->role_id)
                                            ->select('menu_id')->get();
                        $array_menu_id = array();
                        foreach($menu_permission as $data){
                            array_push($array_menu_id, ($data->menu_id));
                        }

                        $main_menus = DB::table('menus')
                                    ->where('is_active', '1')
                                    ->whereIn('id', $array_menu_id)
                                    ->orderBy('seq_no','asc')->orderBy('title','asc')->get();
                        foreach($main_menus as $main_menu){
                            //Main Menu
                            if($main_menu->is_main_menu == 0){
                                echo '<li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">'.$main_menu->title.'</span></li>';
                                // style="color:#fff; background-color:#099a97"
                                // Sub Menu 1
                                // Mencari sub menu 1
                                $sub_menu_1 = DB::table('menus')
                                            ->whereIn('id', $array_menu_id)
                                            ->where('is_main_menu', $main_menu->id)->where('is_active', '1')->orderBy('seq_no','asc')->orderBy('title','asc')->get();
                                //Jika ada sub menu 1 maka tampilkan
                                if(count($sub_menu_1) > 0){
                                    foreach($sub_menu_1 as $sub_menu_1_data){
                                        // Sub Menu 2
                                        // Mencari sub menu 2
                                        $sub_menu_2 = DB::table('menus')
                                                    ->whereIn('id', $array_menu_id)
                                                    ->where('is_main_menu', $sub_menu_1_data->id)->where('is_active', '1')->orderBy('seq_no','asc')->orderBy('title','asc')->get();
                                        //Jika ada sub menu 2 maka tampilkan
                                        if(count($sub_menu_2) > 0){
                                            echo '<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="'.$sub_menu_1_data->icon.'"></i><span class="hide-menu">'.$sub_menu_1_data->title.' </span></a>';
                                            echo '<ul aria-expanded="false" class="collapse  first-level">';
                                            foreach($sub_menu_2 as $sub_menu_2_data){
                                                // Sub Menu 3
                                                // Mencari sub menu 3
                                                $sub_menu_3 = DB::table('menus')
                                                            ->whereIn('id', $array_menu_id)
                                                            ->where('is_main_menu', $sub_menu_2_data->id)->where('is_active', '1')->orderBy('seq_no','asc')->orderBy('title','asc')->get();
                                                //Jika ada sub menu 3 maka tampilkan
                                                if(count($sub_menu_3) > 0){                                        
                                                    echo '<li class="sidebar-item"> <a class="has-arrow sidebar-link" href="javascript:void(0)" aria-expanded="false"><i class="'.$sub_menu_2_data->icon.'"></i> <span class="hide-menu">'.$sub_menu_2_data->title.'</span></a>';
                                                    echo '<ul aria-expanded="false" class="collapse second-level">';
                                                    foreach($sub_menu_3 as $sub_menu_3_data){
                                                        echo '<li class="sidebar-item"><a href="'.URL::to($sub_menu_3_data->url).'" class="sidebar-link"><i class="'.$sub_menu_3_data->icon.'"></i><span class="hide-menu"> '.$sub_menu_3_data->title.'</span></a></li>';
                                                    }
                                                    echo '</ul>';
                                                    echo '</li>';
                                                } else {
                                                    echo '<li class="sidebar-item"><a href="'.URL::to($sub_menu_2_data->url).'" class="sidebar-link"><i class="'.$sub_menu_2_data->icon.'"></i><span class="hide-menu"> '.$sub_menu_2_data->title.' </span></a></li>';
                                                }
                                            }
                                            echo '</ul>';
                                            echo '</li>';
                                        } else {
                                            echo '<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="'.URL::to($sub_menu_1_data->url).'" aria-expanded="false"><i class="'.$sub_menu_1_data->icon.'"></i><span class="hide-menu">'.$sub_menu_1_data->title.' </span></a>';
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>