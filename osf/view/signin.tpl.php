<div class="container" role="main">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <h1 class="text-center">Login</h1>
            <div class="account-wall text-center text-success">
                <!--<i class="fa fa-5x fa-lock"></i>-->
				<div class="row">
					<div class="col-xs-2">
						<a href="<?php print $APP->BASEURL?>/login/sso.php?IDP=Yahoo"><i class="fa fa-2x fa-yahoo"></i></a>
					</div>
					<div class="col-xs-3">
						<a href="<?php print $APP->BASEURL?>/login/sso.php?IDP=Twitter"><i class="fa fa-2x fa-twitter"></i></a>
					</div>
					<div class="col-xs-2">
						<a href="<?php print $APP->BASEURL?>/login/sso.php?IDP=Google"><i class="fa fa-2x fa-google"></i></a>
					</div>
					<div class="col-xs-3">
						<a href="<?php print $APP->BASEURL?>/login/sso.php?IDP=Live"><i class="fa fa-2x fa-windows"></i></a>
					</div>
					<div class="col-xs-2">
						<a href="<?php print $APP->BASEURL?>/login/sso.php?IDP=Facebook"><i class="fa fa-2x fa-facebook"></i></a>
					</div>
				</div>
				<hr/>
				<h4>or</h4>
                <form id="frmLogin" class="form-signin" method="post" action="<?php print $APP->BASEURL?>/login/index.php?ID=2">
                	<?php if ($APP->ERROR['status'] == 1) print '<div class="alert alert-danger" role="alert">'.$APP->ERROR['text'].'</div>';?>
					<div class="form-group">
						<input type="text" id="txtUsername" name="txtUsername" class="form-control" placeholder="Email" required autofocus>
					</div>
					<div class="form-group">
                		<input type="password" id="txtPassword" name="txtPassword" class="form-control" placeholder="Password" required>
					</div>
					<div class="form-group">
                		<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
					</div>
                	<label class="checkbox pull-left">
                    	<input type="hidden" name="hidStatus" id="hidStatus" value="1" />
						<input type="checkbox" value="remember-me"> Remember me</label>
                		<a href="<?php print $APP->BASEURL?>/login/index.php?ID=3" class="pull-right need-help">Need help? </a><span class="clearfix"></span>
                </form>
            </div>
            <!--<h4 class="text-center osf-top-space"><a href="#">Create an account</a></h4>-->
        </div>
    </div>
</div> <!-- /container -->

<script type="text/javascript">
 function submitContactForm()
 {
 	$("#frmLogin").validate();
 	$("#frmLogin").submit();
 }
 </script>
 