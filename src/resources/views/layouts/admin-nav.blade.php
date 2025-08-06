<?php
  $permission = Auth::user()->permission;
  $permissionurl = array();
  $firstpage_admin = '/admin';

  //if (strpos($permission, ',0,') !== false) {
    array_push($permissionurl, 'admin/users','admin/profile');
  //}
  if (strpos($permission, ',1,') !== false) {
    array_push($permissionurl, 'admin/settings');
  }
  if (strpos($permission, ',2,') !== false) {
    array_push($permissionurl, 'admin/banner');
  }
  if (strpos($permission, ',3,') !== false) {
    array_push($permissionurl, 'admin/media');
  }
  if (strpos($permission, ',4,') !== false) {
    array_push($permissionurl, 'admin/student');
  }
  if (strpos($permission, ',5,') !== false) {
    array_push($permissionurl, 'admin/payment','admin/paymentdetail');
  }
  if (strpos($permission, ',6,') !== false) {
    array_push($permissionurl, 'admin/template');
  }
  if (strpos($permission, ',7,') !== false) {
    array_push($permissionurl, 'admin/maillist');
  }
  if (strpos($permission, ',8,') !== false) {
    array_push($permissionurl, 'admin/report');
  }

  if ($_SERVER['REQUEST_URI'] != $firstpage_admin) {
    if (Helper::activeThis($permissionurl) == 'active') {
      //echo 'active';
    } else {
      //echo 'no active';
      header('Location: '. Helper::url('admin'));
      exit();
    }
  }

  $MENU_PAYMENT = ['admin/payment', 'admin/paymentdetail'];
?>
<ul class="sidebar-menu">
    <li class="header">{{ trans('admin.mainmenu') }}</li>

    @if (strpos($permission, ',0,') !== false)
      <li class="{{ Helper::activeThis(['admin/users']) }}">
          <a href="{{ Helper::url('admin/users') }}"><i class="fa fa-user"></i>
              <span>User</span></a>
      </li>
    @endif

    @if (strpos($permission, ',1,') !== false)
      <li class="{{ Helper::activeThis(['admin/settings']) }}">
          <a href="{{ Helper::url('admin/settings') }}"><i class="fa fa-cogs"></i>
              <span>{{ trans('admin.settings') }}</span></a>
      </li>
    @endif

    @if (strpos($permission, ',2,') !== false)
      <!--<li class="{{ Helper::activeThis(['admin/banner']) }}">
          <a href="{{ Helper::url('admin/banner') }}"><i class="fa fa-exchange"></i>
              <span>{{ trans('admin.banners') }}</span></a>
      </li>-->
    @endif

    @if (strpos($permission, ',3,') !== false)
      <li class="{{ Helper::activeThis(['admin/media']) }}">
          <a href="{{ Helper::url('admin/media') }}" target="_blank"><i class="fa fa-picture-o"></i>
              <span>{{ trans('admin.medias') }}</span></a>
      </li>
    @endif

    <!-- Manage Student -->
    @if (strpos($permission, ',4,') !== false)
      <li class="{{ Helper::activeThis(['admin/student']) }}">
          <a href="{{ Helper::url('admin/student') }}"><i class="fa fa-graduation-cap"></i>
              <span>{{ trans('menu.manage-student') }}</span></a>
      </li>
    @endif

    <!-- Payment -->
    @if (strpos($permission, ',5,') !== false)
      <li class="treeview {{ Helper::activeThis($MENU_PAYMENT) }}">
          <a href="#">
              <i class="fa fa-credit-card"></i>
              <span>{{ trans('menu.payment') }}</span>
              <i class="fa fa-angle-right pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="{{ Helper::setActive('admin/payment') }}">
                <a href="{{ Helper::url('admin/payment') }}"><i class="fa fa-credit-card"></i>
                    <span>{{ trans('menu.payment-master') }}</span></a>
            </li>
            <li class="{{ Helper::setActive('admin/paymentdetail') }}">
                <a href="{{ Helper::url('admin/paymentdetail') }}"><i class="fa fa-info"></i>
                    <span>{{ trans('menu.payment-detail') }}</span></a>
            </li>
          </ul>
      </li>
    @endif

    <!-- Templates -->
    @if (strpos($permission, ',6,') !== false)
      <li class="{{ Helper::activeThis(['admin/template']) }}">
        <a href="{{ Helper::url('admin/template') }}"><i class="fa fa-file-text-o"></i>
          <span>{{ trans('admin.lists-templates') }}</span></a>
      </li>
    @endif

    <!-- Templates -->
    @if (strpos($permission, ',7,') !== false)
      <li class="{{ Helper::activeThis(['admin/maillist']) }}">
        <a href="{{ Helper::url('admin/maillist') }}"><i class="fa fa-envelope-o"></i>
          <span>Mail Lists</span></a>
      </li>
    @endif

    <!-- Report -->
    @if (strpos($permission, ',8,') !== false)
      <li class="{{ Helper::activeThis(['admin/report']) }}">
        <a href="{{ Helper::url('admin/report') }}"><i class="fa fa-file-o"></i>
          <span>Report</span></a>
      </li>
    @endif
</ul>
