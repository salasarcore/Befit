<?php
include_once('conn.php');
@include_once('functions/common.php');
if(isset($_POST['login_submit']))
{
$email=makeSafe($_POST['email']);
$pass=makeSafe($_POST['password']);

$sql="SELECT `user_id`, `full_name`, `designation`, `state_id`, `district_id`, `sub_division_id`, `login_type`, `mime` FROM `site_users` where `email`='".$email."' and `user_pass`='".$pass."'";
$result  = mysql_query($sql) or die('Error, query failed'.mysql_error());
if(mysql_affected_rows()>0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$_SESSION['user_id']=$row['user_id'];
		$_SESSION['full_name']=$row['full_name'];
		$_SESSION['designation']=$row['designation'];
		$_SESSION['state_id']=$row['state_id'];
		$_SESSION['district_id']=$row['district_id'];
		$_SESSION['sub_division_id']=$row['sub_division_id'];
		$_SESSION['user_type']=$_SESSION['login_type']=$row['login_type'];
		$_SESSION['mime']=$row['mime'];
		
		echo"<script>location.href='profile.php'</script>";
	
	

	}
	else
	$message="<p class=\"bg-danger\">Invalid username and password.</p>";
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <title>PSP | Welcome...</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Favicon -->
   
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- CSS Global Compulsory -->
    <link rel="stylesheet" href="../plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../plugins/bootstrap/css/bootstrap-theme.css">
    <link rel="stylesheet" href="css/style.css">
  

    <!-- CSS Implementing Plugins -->
    
    <link rel="stylesheet" href="../plugins/font-awesome/css/font-awesome.min.css">
    
    <!-- CSS Page Style -->    
   

    <!-- CSS Theme -->    
    <link rel="stylesheet" href="../plugins/sky-forms/version-2.0.1/css/sky-forms.css">
    <link rel="stylesheet" href="../plugins/sky-forms/version-2.0.1/css/custom-sky-forms.css">
    

    <!-- CSS Customization -->
    <link rel="stylesheet" href="css/custom.css">
</head> 
<body class="boxed container">
	<div class="wrapper">
		
<nav class="navbar navbar-default  margin-bottom-0" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><img src="../logo.png" height=80px style="margin-top:-15px" /></a>
    </div>

  </div><!-- /.container-fluid -->
</nav>
		
		<div class="container content">
			<div class="row login_back">
				
					<div class="col-md-4 pull-right" style="margin:0px 5px;opacity:.9">
					
                        <form class="sky-form" id="sky-form1" action="index.php" novalidate="novalidate" method="POST">
                            <header>Login</header>
                            
                            <fieldset>          
								<?php echo @$message;?>							
                                <section>
                                    <div class="row">
                                        <label class="label col col-4">E-mail</label>
                                        <div class="col col-8">
                                            <label class="input">
                                                <i class="icon-append fa fa-user"></i>
                                                <input type="email" name="email">
												<b class="tooltip tooltip-bottom-right">Needed to enter email id</b>
                                            </label>
                                        </div>
                                    </div>
                                </section>
                                
                                <section>
                                    <div class="row">
                                        <label class="label col col-4">Password</label>
                                        <div class="col col-8">
                                            <label class="input">
                                                <i class="icon-append fa fa-lock"></i>
                                                <input type="password" name="password">
												<b class="tooltip tooltip-bottom-right">Needed to enter password</b>
                                            </label>
                                            
                                        </div>
                                    </div>
                                </section> 
								<!--section>
                                    <div class="row">
                                        <label class="label col col-4">Login type</label>
                                   <div class="col col-8"> 
						<label class="select">
							<select name="login_type" class="valid">
								<option value="BLOCK">BLOCK</option>
								<option value="SUB-DIVISION">SUB-DIVISION</option>
								<option value="DISTRICT">DISTRICT</option>
								<option value="STATE">STATE</option>
								<option value="CEO">CEO</option>
								<option value="GUEST">GUEST</option>
							</select>
							<i></i>
						</label>
					
                                    </div>
                                    </div>
                                </section-->
                                
                                <section>
                                    <div class="row">
                                        <div class="col col-4"></div>
                                        <div class="col col-8">
                                            <label class="checkbox"><input type="checkbox" checked="" name="remember"><i></i>Keep me logged in</label>
                                        </div>
                                    </div>
                                </section>
                            </fieldset>
                            <footer>
                                <button class="btn btn-info" type="submit" name="login_submit" value="submit">Log in</button>
                               
                            </footer>
                        </form>         
                        
                      
                    </div>
				
			</div>
		</div>
		<?php include('php/copyright.php');?>

		
    
	</div>



	<!-- JS Global Compulsory -->           
	<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="../js/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="../plugins/bootstrap/js/bootstrap.min.js"></script> 
	<script type="text/javascript" src="../js/back-to-top.js"></script>
	
	<!-- JS Implementing Plugins -->           
	
<script src="../plugins/sky-forms/version-2.0.1/js/jquery.min.js"></script>
		<script src="../plugins/sky-forms/version-2.0.1/js/jquery.validate.min.js"></script>
		<!--[if lt IE 10]>
			<script src="../plugins/sky-forms/js/version-2.0.1/jquery.placeholder.min.js"></script>
		<![endif]-->	
	<script type="text/javascript">
			$(function()
			{
				// Validation for login form
				$("#sky-form").validate(
				{					
					// Rules for form validation
					rules:
					{
						email:
						{
							required: true,
							email: true
						},
						password:
						{
							required: true,
							minlength: 3,
							maxlength: 20
						}
					},
										
					// Messages for form validation
					messages:
					{
						email:
						{
							required: 'Please enter your email address',
							email: 'Please enter a VALID email address'
						},
						password:
						{
							required: 'Please enter your password'
						}
					},					
					
					// Do not change code below
					errorPlacement: function(error, element)
					{
						error.insertAfter(element.parent());
					}
				});
				});
				
				
		</script>
	<!--[if lt IE 9]>
		<script src="../plugins/respond.js"></script>
	<![endif]-->			
<body>
</html>