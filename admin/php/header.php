<div class="header">
	  <!-- Static navbar -->
      <div class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><img src="../logo.png" height="50px"  style="z-index:20;margin-top:-15px" /></a>
          </div>
          <div class="navbar-collapse collapse">
          
            <!-- Left nav -->
            <ul class="nav navbar-nav">
             
				<li><a href="#">Administration</a>
					<ul class="dropdown-menu">
						<li><a href="pages.php?src=session_list.php">Session Master</a></li>
						<li><a href="pages.php?src=employee_department_list.php">Employee Department Master</a></li>
						<li><a href="pages.php?src=department_list.php">Course Master</a></li>
						<li><a href="pages.php?src=session_section_list.php">Course Wise Batches</a></li>
						<li><a href="#">Contact Category</a>
							<ul class="dropdown-menu">
								<li><a href="pages.php?src=contact_category.php">Contact Category Master</a></li>
								<li><a href="pages.php?src=contact_us.php">List of Enquirers</a></li>
		 
							</ul> 
						</li>
					 </ul>
				</li>	
				<li><a href="#">Admission</a>
					<ul class="dropdown-menu">
					  <li><a href="pages.php?src=applied_student_list.php">Applied Candidates</a></li>
					  <li><a href="pages.php?src=admission_register.php">Registered Candidates</a></li>
					  <li><a href="pages.php?src=admitted_student_list.php">Course-wise Candidates</a></li>
					 </ul> 
				  </li>	
				  <li><a href="#">Member</a>
					<ul class="dropdown-menu">
					  <li><a href="pages.php?src=student_attendance.php">Member Attendance</a></li>
					</ul> 
				  </li>	
				<li><a href="#" >Fees</a>
					<ul class="dropdown-menu">
						<li><a href="pages.php?src=fee/fee_category_list.php">Fee Category Master</a></li>
						<li><a href="pages.php?src=fee/fee_discount_list.php">Fee Discount Master</a></li>
						<li><a href="pages.php?src=fee/fee_fine_list.php">Fee Fine Master</a></li>
						<li><a href="pages.php?src=fee/fee_expected_list.php">Fee Expected</a></li>
						<li><a href="pages.php?src=fee/monthly_expected_list.php">Monthly Expected Fees</a></li>
						<li><a href="pages.php?src=fee/fee_collect.php">Fee Collection</a></li>
						<li><a href="pages.php?src=bank_list.php">Bank Master</a></li>
						<li><a href="#">Reports</a>
							  <ul class="dropdown-menu">
								<li><a href="pages.php?src=fee/fee_discount_report.php">Fee Discount</a></li>
							  </ul>
						</li>
					 </ul>
				</li>
				 
				<li><a href="#">Payroll</a>
					<ul class="dropdown-menu">
						<li><a href="pages.php?src=employee_list.php">Employee List</a></li>
						<li><a href="pages.php?src=salary/employee_salary_setting.php">Employee Salary Setting</a></li>
						<li><a href="pages.php?src=employee_attendance.php">Employee Monthly Attendance</a></li>
						<!-- <li><a href="pages.php?src=academic_calender.php">Employee Hourly Attendance</a></li>-->
						<li><a href="pages.php?src=salary/pay_allowances_list.php">Pay & Allowances Type</a></li> 
						<li><a href="pages.php?src=salary/pay_deduction_list.php">Pay Deduction Type</a></li> 
						<li><a href="pages.php?src=salary/pay_loan_purpose_list.php">PF Loan Purpose</a></li>
						<li><a href="pages.php?src=salary/employee_personal_information.php">Employee Pay Structure</a></li>
						<li><a href="pages.php?src=salary/scroll_for_next_month.php">Scroll For Next Month</a></li>
						<li><a href="#">Reports</a> 
							<ul class="dropdown-menu"> 
								  <li><a href="pages.php?src=salary/pay_slip.php">Pay Slip</a></li>
								  
							</ul> 
						</li> 
					</ul> 
				  </li>	 
				 <li><a href="#">Asset</a>
					<ul class="dropdown-menu">
						<li><a href="pages.php?src=asset/asset_list.php">Asset Master</a></li>
						<li><a href="pages.php?src=asset/asset_record_list.php">Asset Record List</a></li>
					  
					 </ul> 
				  </li>	 
				<li><a href="#">Expense</a>
					<ul class="dropdown-menu">
						<li><a href="pages.php?src=expense/expense_category_list.php">Expense Category</a></li>
						<li><a href="pages.php?src=expense/expense_list.php">Expense List</a></li>
										  
					 </ul> 
				 </li>	 
				<li><a href="#">Utility</a>
					<ul class="dropdown-menu">
					  <!--li><a href="pages.php?src=academic_calender.php">Academic Calendar</a></li-->
					    <li><a href="#" class="arrow"> Email/SMS Notification</a>
	  <ul class="dropdown-menu">
	
	<li><a href="pages.php?src=notification_settings.php">Notification Settings</a></li>
	<li><a href="#" class="arrow">Manual SMS</a>
	  <ul class="dropdown-menu">
	  <li><a href="pages.php?src=sms/manual_student_sms.php">Member</a></li>
	  <li><a href="pages.php?src=sms/manual_employee_sms.php">Employee</a></li>
	  </ul>
	  </li>
	  <li><a href="#" class="arrow">Transactional SMS</a>
	  <ul class="dropdown-menu">
	  <li><a href="pages.php?src=sms/tran_student_sms.php">Member</a></li>
	  <li><a href="pages.php?src=sms/tran_employee_sms.php">Employee</a></li>	  </ul>
	  </li>
	  <li><a href="#" class="arrow">For Nonregistered Users</a>
	  <ul class="dropdown-menu">
	  <li><a href="pages.php?src=sms/contacts.php">Add Contacts</a></li>
	<li><a href="pages.php?src=sms/contacts_list.php">List Contacts</a></li>
		  <li><a href="pages.php?src=sms/nonregistered_sendsms.php">Send SMS</a></li>
	  </ul>
	  </li>
	  <li><a href="pages.php?src=sms/sms_log.php">SMS Log</a></li>
	  </ul>
	  </li>
					  <li><a href="pages.php?src=notice_list.php">Notification</a></li>
					  <li><a href="pages.php?src=gallery_list.php">Image Gallery</a></li>
					  <li><a href="pages.php?src=news_list.php">News/Press/Media</a></li>
					  
					 </ul> 
				 </li>	 
					
                 
             </ul>
             
          
            <!-- Right nav -->
            <ul class="nav navbar-nav navbar-right">
              <li><a href="bootstrap-navbar.html">Welcome:<?php echo $_SESSION['emp_name'];?></a></li>
              <li><a href="#">My Account</a>
                <ul class="dropdown-menu">
                  <li><a href="pages.php?src=change_pass.php">Change Password</a>
				  
				  
				  </li>
				  <li><a href="logout.php" >Logout</a></li>
                  
                </ul>
              </li>
            </ul>
          
          </div><!--/.nav-collapse -->
        </div><!--/.container -->
      </div>

	
			</div>	
			
        <!-- End Navigation -->