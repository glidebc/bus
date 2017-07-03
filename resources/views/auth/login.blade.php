@include('admin.partials.header')
<div style="margin-top: 10%;"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

          <!--效果html开始-->
          <div class="htmleaf-container">
            <div class="wrapper">
              <div class="container">
                <h1>{{ trans('quickadmin::admin.partials-header-title') }}</h1>

                <!-- <div class="panel-heading">{{ trans('quickadmin::auth.login-login') }}</div> -->
                <div class="panel-body">
                    @if (count($errors) > 0)
                        <div class="alert-area text-danger">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                        <br>
                    @endif

                    <form class="form-horizontal"
                          role="form"
                          method="POST"
                          action="{{ url('login') }}">
                        <input type="hidden"
                               name="_token"
                               value="{{ csrf_token() }}">

                        <div class="form-group">
                            <!-- <label class="col-md-4 control-label">{{ trans('quickadmin::auth.login-email') }}</label> -->

                            <div>
                                <input type="email"
                                       
                                       name="email"
                                       autofocus="autofocus"
                                       placeholder="{{ trans('quickadmin::auth.login-email') }}"
                                       value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <!-- <label class="col-md-4 control-label">{{ trans('quickadmin::auth.login-password') }}</label> -->

                            <div>
                                <input type="password"
                                       
                                       name="password"
                                       placeholder="{{ trans('quickadmin::auth.login-password') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div>
                                <label>
                                    <input type="checkbox"
                                           name="remember"> {{ trans('quickadmin::auth.login-remember_me') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div>
                                <button type="button"
                                        class="btn"
                                        id="login-button">
                                    {{ trans('quickadmin::auth.login-btnlogin') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

              </div>
  
            <ul class="bg-bubbles">
              <li></li>
              <li></li>
              <li></li>
              <li></li>
              <li></li>
              <li></li>
              <li></li>
              <li></li>
              <li></li>
              <li></li>
            </ul>
          </div>
        </div>
        <script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
        <!-- <script src="js/jquery-2.1.1.min.js" type="text/javascript"></script> -->
        <script>
        $('#login-button').click(function (event) {
            event.preventDefault();
            $('.alert-area').remove();
            $('form').fadeOut(500);
            $('.wrapper').addClass('form-success');
            setTimeout(function() {
                $('form').submit();
            }, 1000);
        });

        $('form').keypress(function(e) {
            if(e.which == 13) {
                $('#login-button').click();
            }
        });
        </script>
        <!--效果html结束-->

            </div>
        </div>
    </div>
</div>

<style type="text/css">
body ::-webkit-input-placeholder {
  /* WebKit browsers */
  color: white;
  font-weight: 300;
}
body :-moz-placeholder {
  /* Mozilla Firefox 4 to 18 */
  color: white;
  opacity: 1;
  font-weight: 300;
}
body ::-moz-placeholder {
  /* Mozilla Firefox 19+ */
  color: white;
  opacity: 1;
  font-weight: 300;
}
body :-ms-input-placeholder {
  /* Internet Explorer 10+ */
  color: white;
  font-weight: 300;
}
.wrapper {
  background: #50a3a2;
  background: -webkit-linear-gradient(top left, #50a3a2 0%, #53e3a6 100%);
  background: linear-gradient(to bottom right, #50a3a2 0%, #53e3a6 100%);
  opacity: 0.8;
  position:relative;
  left: 0;
  width: 100%;
  /*height: 400px;*/
  overflow: hidden;

}

.wrapper.form-success .container h1 {
  -webkit-transform: translateY(85px);
      -ms-transform: translateY(85px);
          transform: translateY(85px);
}
.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 2px 0;/*80px 0;*/
  height: 420px;
  text-align: center;
}
.container h1 {
  font-size: 40px;
  -webkit-transition-duration: 1s;
          transition-duration: 1s;
  -webkit-transition-timing-function: ease-in-put;
          transition-timing-function: ease-in-put;
  font-weight: 200;
  color:#fff;
}
form {
  /*padding: 20px 0;*/
  position: relative;
  z-index: 2;
}
.alert-area {
  width: 220px;
  margin: 0 auto;
}
form input:not([type=checkbox]) {
  -webkit-appearance: none;
     -moz-appearance: none;
          appearance: none;
  outline: 0;
  border: 1px solid rgba(255, 255, 255, 0.4);
  background-color: rgba(255, 255, 255, 0.2);
  width: 220px;
  border-radius: 3px;
  padding: 10px 15px;
  margin: 0 auto 10px auto;
  display: block;
  text-align: center;
  font-size: 18px;
  color: white;
  -webkit-transition-duration: 0.25s;
          transition-duration: 0.25s;
  font-weight: 300;
}
form input:not([type=checkbox]):hover {
  background-color: rgba(255, 255, 255, 0.4);
}
form input:not([type=checkbox]):focus {
  background-color: white;
  width: 300px;
  color: #53e3a6;
}
form button {
  -webkit-appearance: none;
     -moz-appearance: none;
          appearance: none;
  outline: 0;
  background-color: white;
  border: 0;
  padding: 10px 15px;
  color: #53e3a6;
  border-radius: 3px;
  width: 220px;/*250px;*/
  cursor: pointer;
  font-size: 18px;
  -webkit-transition-duration: 0.25s;
          transition-duration: 0.25s;
}
form button:hover {
  background-color: #f5f7f9;
}
.bg-bubbles {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
}
.bg-bubbles li {
  position: absolute;
  list-style: none;
  display: block;
  width: 40px;
  height: 40px;
  background-color: rgba(255, 255, 255, 0.15);
  bottom: -160px;
  -webkit-animation: square 25s infinite;
  animation: square 25s infinite;
  -webkit-transition-timing-function: linear;
  transition-timing-function: linear;
}
.bg-bubbles li:nth-child(1) {
  left: 10%;
}
.bg-bubbles li:nth-child(2) {
  left: 20%;
  width: 80px;
  height: 80px;
  -webkit-animation-delay: 2s;
          animation-delay: 2s;
  -webkit-animation-duration: 17s;
          animation-duration: 17s;
}
.bg-bubbles li:nth-child(3) {
  left: 25%;
  -webkit-animation-delay: 4s;
          animation-delay: 4s;
}
.bg-bubbles li:nth-child(4) {
  left: 40%;
  width: 60px;
  height: 60px;
  -webkit-animation-duration: 22s;
          animation-duration: 22s;
  background-color: rgba(255, 255, 255, 0.25);
}
.bg-bubbles li:nth-child(5) {
  left: 70%;
}
.bg-bubbles li:nth-child(6) {
  left: 80%;
  width: 120px;
  height: 120px;
  -webkit-animation-delay: 3s;
          animation-delay: 3s;
  background-color: rgba(255, 255, 255, 0.2);
}
.bg-bubbles li:nth-child(7) {
  left: 32%;
  width: 160px;
  height: 160px;
  -webkit-animation-delay: 7s;
          animation-delay: 7s;
}
.bg-bubbles li:nth-child(8) {
  left: 55%;
  width: 20px;
  height: 20px;
  -webkit-animation-delay: 15s;
          animation-delay: 15s;
  -webkit-animation-duration: 40s;
          animation-duration: 40s;
}
.bg-bubbles li:nth-child(9) {
  left: 25%;
  width: 10px;
  height: 10px;
  -webkit-animation-delay: 2s;
          animation-delay: 2s;
  -webkit-animation-duration: 40s;
          animation-duration: 40s;
  background-color: rgba(255, 255, 255, 0.3);
}
.bg-bubbles li:nth-child(10) {
  left: 90%;
  width: 160px;
  height: 160px;
  -webkit-animation-delay: 11s;
          animation-delay: 11s;
}
@-webkit-keyframes square {
  0% {
    -webkit-transform: translateY(0);
            transform: translateY(0);
  }
  100% {
    -webkit-transform: translateY(-700px) rotate(600deg);
            transform: translateY(-700px) rotate(600deg);
  }
}
@keyframes square {
  0% {
    -webkit-transform: translateY(0);
            transform: translateY(0);
  }
  100% {
    -webkit-transform: translateY(-700px) rotate(600deg);
            transform: translateY(-700px) rotate(600deg);
  }
}
</style>
@include('admin.partials.footer')
