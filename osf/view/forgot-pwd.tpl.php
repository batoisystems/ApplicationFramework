<?php $iTabIndex = 0; ?>
<!-- Main content starts -->
<?php if(isset($sErrMsg) && ($sMsg == '')) { ?>
	<form id="forgetPwd" action="./sign.php?ID=4" method="post">
		<div class="ui-widget ui-corner-bl ui-corner-br" style="margin:0px auto;width:960px;background-color:#FFFFFF;border:1px solid #FFFFFF;">
			<div style="margin:0px auto;width:500px;">
			<h1>Forgot Password?</h1>
			<h3>Use the the email that you entered when you set up your User account</h3>
			<div class="ui-widget-content ui-corner-all" style="overflow:hidden;padding:1em;">
			<p><label>Email<span class="mandatory">*</span></label><br/><input type="text" id="txtEmailId" name="txtEmailId" style="float:left;clear:both;width:200px;" class="required" tabindex="<?php print $iTabIndex + 1;?>"/></p>
			<p style="float:left;clear:both;margin-top:10px;"><button class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" onclick="javascript:submitPwdForm();" tabindex="<?php print $iTabIndex + 1;?>">Submit</button></p>
			</div>
			<br /><br /><br />
			</div>
		</div>
		<input id="hidPwd" name="hidPwd" size="30" type="hidden" value="2" />
		</form>
		<?php } else { ?>
		<div class="ui-widget ui-corner-bl ui-corner-br" style="margin:0px auto;width:960px;background-color:#FFFFFF;border:1px solid #FFFFFF;">
			<div style="margin:0px auto;width:500px;">
			<h1>Forgot Password?</h1>
			<h3>Use the the email that you entered when you set up your User account</h3>
			<div class="ui-widget-content ui-corner-all" style="overflow:hidden;padding:1em;">
			<?php print '<p style="color:#000000;font-weight:normal;line-height:18px;" >'.$sMsg.'</p>'; ?>
			</div>
			<br /><br /><br />
			</div>
		</div>
		<?php } ?>
		<script type="text/javascript">
		 function submitPwdForm()
		 {
		 	$("#forgetPwd").validate();
		 	$("#forgetPwd").submit();
		 }
 		</script>
<!-- Main content ends -->


