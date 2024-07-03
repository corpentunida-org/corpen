<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <x-component-header />
</head>

<body>
  <div class="wrapper ">
    @include('layouts.navbar')
    <div class="main-panel" id="main-panel">

      <nav class="navbar navbar-expand-lg navbar-transparent  bg-primary  navbar-absolute">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand" href="#">Hola, {{ Auth::user()->name }}</a>
          </div>
          <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="navbar-collapse justify-content-end collapse" id="navigation">
            <x-input-search></x-input-search>
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="/user/profile">
                  <i class="now-ui-icons users_single-02"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Mi cuenta</span>
                  </p>
                </a>
              </li>
            </ul>            
          </div>
        </div>
      </nav>
      <!-- End Navbar -->

      <div class="panel-header panel-header-lg vh-100">
        <!-- <canvas id="bigDashboardChart"></canvas> -->

      </div>


      @include('layouts.footer')

    </div>
  </div>

  <x-footer-jslinks></x-footer-jslinks>
  
</body>

 </html>