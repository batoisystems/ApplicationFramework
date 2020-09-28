<div class="container" role="main">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <h2 class="text-center">Multi-factor Authentication - OTP Verification</h2>
            <h5>Please enter the OTP you have received through email/SMS (as appropriately setup by your Application Administrator) in the form filed below:</h5>
            <div class="account-wall text-center text-success">
                <!--<i class="fa fa-5x fa-lock"></i>-->
                <form id="frmLogin" class="form-signin" method="post" action="<?php print $APP->BASEURL?>/login/index.php?ID=2">
                	<?php
                    if($APP->ERROR['text'] == 0) $sColorStr = 'danger';else $sColorStr = 'success';
                    if ($APP->ERROR['text'] != '') print '<div class="alert alert-'.$sColorStr.'" role="alert">'.$APP->ERROR['text'].'</div>';?>
					<div class="form-group">
						<input type="text" id="otpText" name="otpText" class="form-control" required autofocus>
					</div>
					<div class="form-group">
                		<input type="hidden" name="hidStatus" id="hidStatus" value="2" />
                		<button class="btn btn-lg btn-primary btn-block" type="submit">Submit OTP</button>
					</div>
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
 