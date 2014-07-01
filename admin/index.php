<?php
include_once('conn.php');
@include_once('functions/common.php');
include("functions/comm_functions.php");
$message="";
if(isset($_POST['login_submit']))
{
	@$uname= makeSafe($_POST['email']);
	@$pass=@$_POST['password'];
	@$d_session=makeSafe($_POST['session']);
	if(trim(@$uname)=="")
	{
		$message="<div class='error'>Please enter your Employee ID</div>";
	}
	else if(@$pass=="")
	{
		$message="<div class='error'>Please enter your password</div>";
	}
	
	else
	{
		$query1="SELECT emp_id FROM employee where activated='Y' and access_level IN('Admin','Super Admin','User')";

		$res=mysql_query($query1,$link) or die('Error, query failed 1');
	
		if(mysql_affected_rows($link)>0)
		{
			$query="SELECT empid,emp_id,time_from,time_to,employee.br_id,br_name, emp_name,access_level, activated FROM employee,mst_branch where
					employee.br_id=mst_branch.br_id and emp_id='".mysql_real_escape_string($uname)."' and password='".mysql_real_escape_string($pass)."'";
			$result  = mysql_query($query);
	
			$row2 = array();
			$trow = array();
			$prow = array();
			$query2 = "SELECT SUM(spd.no_of_transactional_purchase) as sms_t_count, SUM(spd.no_of_promotional_purchase) as sms_p_count FROM  school_sms_purchase_dtls spd ";
				
			$res2 = mysql_query($query2);
			if($res2)
			{
				$row2 = mysql_fetch_array($res2);
	
				$tquery = "select count(sms_type) as t_count from sms_transaction_log where sms_type='T'";
				$pquery = "select count(sms_type) as p_count from sms_transaction_log where sms_type='P'";
				$tres = mysql_query($tquery,$link);
				$pres = mysql_query($pquery,$link);
				if($tres)
					$trow = mysql_fetch_assoc($tres);
				if($pres)
					$prow = mysql_fetch_assoc($pres);
			}
	
			if(mysql_num_rows($result)>0)
			{
				$sql="select CURTIME() as timing";
				$result1=mysql_query($sql,$link);
				$row=mysql_fetch_assoc($result1);
				$curtime=strtotime($row['timing']);
	
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
				if($row['activated']=="N")
					$message="<div class='error'>Your login disabled, Please contact administrator</div>";
				elseif($row['access_level']=="")
				$message="<div class='error'>User access level is not set. Please contact system administrator</div>";
				else
				{
					if($row['access_level']=="User")
					{
						if($curtime<strtotime($row['time_from']) || $curtime>strtotime($row['time_to']))
							$message="<div class='error'>Access denied(you are allowed to login from ".date('g.iA', strtotime($row['time_from']))." to ".date('g.iA', strtotime($row['time_to'])).")</div>";
						else{
							$sqlquery="select * from admin_access_perams where empid=".$row["empid"];
							$accessdetails=mysql_fetch_array(mysql_query($sqlquery,$link));
							$sqlquery.=" and NOW() BETWEEN fromdatetime and todatetime";
							if(empty($accessdetails))
								$message="<div class='error'>You are not authorised to access the system. Please contact system administrator</div>";
							else
							{
								if($accessdetails['is_permanent']!="1")
								{
									$sqlcount=mysql_num_rows(mysql_query($sqlquery,$link));
									if($sqlcount<=0)
										$message="<div class='error'>You are allowed to login from ".date('jS-M-Y g.iA', strtotime($accessdetails['fromdatetime']))." to ".date('jS-M-Y g.iA', strtotime($accessdetails['todatetime']))." only</div>";
								}
							}
						}
					}
					if($message=="")
					{
						$_SESSION['empid']=$row["empid"];
						$_SESSION['emp_name']=$row["emp_name"];
						$_SESSION['emp_id']=$row["emp_id"];
						$_SESSION['activated']=$row['activated'];
						$_SESSION['br_id']=1;
						$_SESSION['br_name']=$row['br_name'];
						$_SESSION['d_session']=$d_session;
						if($row2['sms_p_count']!=NULL)
							$_SESSION['sms_p_count'] = $row2['sms_p_count'] - $prow['p_count'];
						if($row2['sms_t_count']!=NULL)
							$_SESSION['sms_t_count'] = $row2['sms_t_count'] - $trow['t_count'];
	
						$_SESSION['access_level']=$row['access_level'];
	
						unset($_SESSION['loginAttempts_b']);
						create_session();
						echo "<script>location.href='pages.php'</script>";
					}
				}
			}
			
		}
	
		else
		{
			if($uname=="admin" && $pass=="admin#123")
			{
				$_SESSION['empid']="Admin";
				$_SESSION['emp_name']="Admin";
				$_SESSION['emp_id']="0";
				$_SESSION['activated']="Y";
				$_SESSION['br_id']="1";
				$_SESSION['access_level']="Super Admin";
				$query="SELECT br_name FROM mst_branch where mst_branch.br_id =1";
				$result  = mysql_query($query,$link);
				if(mysql_affected_rows($link)>0)
				{
					$row     = mysql_fetch_array($result, MYSQL_ASSOC);
					$_SESSION['br_name']=$row['br_name'];
				}
				$_SESSION['d_session']=$d_session;
				
				create_session();
				echo "<script>location.href='pages.php'</script>";
					
			}
	
		}
	
	
	
	}//else
	}//login
	
	
	
	
	


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
                                <section>
                                    <div class="row">
                                        <label class="label col col-4">Session</label>
                                        <div class="col col-8">
                                            <label class="select" name="session">
                                                <i class="icon-append fa fa-lock"></i>
                                                <?php session_default(@$session);?>
												<b class="tooltip tooltip-bottom-right">Select the session</b>
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