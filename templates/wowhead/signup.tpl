{include file='header.tpl'}
<script src="templates/wowhead/js/recaptcha.js?" type="text/javascript"></script>

		<div id="main">
				<div id="main-precontents"></div>
				<div id="main-contents">
					<div class="pad3"></div>
					<script type="text/javascript">
						function inputBoxValidate(f)
						{ldelim}
							var _ = f.elements[0];
							if(_.value.length == 0)
							{ldelim}
								ge('inputbox-error').innerHTML = LANG.message_enterusername;
								_.focus();
								return false;
							{rdelim}

							_ = f.elements[1];
							if(_.value.length == 0)
							{ldelim}
								ge('inputbox-error').innerHTML = LANG.message_enterpassword;
								_.focus();
								return false;
							{rdelim}

							_ = f.elements[2];
							if(_.value.length == 0)
							{ldelim}
								ge('inputbox-error').innerHTML = message_passwordsdonotmatch;
								_.focus();
								return false;
							{rdelim}
						{rdelim}
					</script>
                    <div class="home-search">
					<form action="?account=signup" method="post" onsubmit="return inputBoxValidate(this)">
						<div class="inputbox" style="position: relative">
							<h1>{#Create_your_account#}</h1>
							<div id="inputbox-error">{$signup_error}</div>

							
                            <p><label class="label">{#Username#}</label> 
                            <input type="text" name="username" value="" maxlength="16" id="username-generic" class="input" /></p> 
                            
                            <p><label class="label">{#Password#}</label> 
                            <input type="password" name="password" maxlength="16" class="input" /></p>
                            
                            
                            <p><label class="label">{#Confirm_password#}</label> 
                            <input type="password" name="c_password" maxlength="16" class="input" /></p>
                            
                            <p><label class="label">{#Email#}</label> 
                            <input type="text" name="email" class="input" /></p>
                                                       
                            <a href="javascript:Recaptcha.reload()"><div id="recaptcha_image"></div></a></p>
                            
                            <div id="recaptcha_widget" style="display:none">
                                <p><label class="label">{#reCapcha_text#}</label>
                                <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="input"/></p> 
                            </div> 
 
                            <script type="text/javascript"
                                src="http://www.google.com/recaptcha/api/challenge?k=6LeWKcASAAAAAEk0meQUJaBeeEu43JLtsjOp-vrV"> 
                            </script> 
                
                                <table align="center">                                
									<td align="center">
										<div class="pad2"></div>
										<input type="submit" name="signup" value="{#Signup#}" />
									</td>
								</tr>
							</table>
						</div>
					</form>
                    </div>

					<script type="text/javascript">ge('username-generic').focus()</script>
					<div class="clear"></div>
				</div>

			</div>
		</div>
{include file='footer.tpl'}
