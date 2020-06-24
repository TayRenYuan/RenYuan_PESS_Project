<style>
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 50%;
}	
.topnav {
		width: 100vw;
		margin-left: -8px;
	}
	.icon {
	display: block;
	position: absolute;
  	left: 40px;
		max-height: 50px;
		max-width: 50px;
	}	
</style>


<div id="banner"><img src="Banner.png" class="center"></div>

<div><a class="<?php if($page=='rylogcall'){echo 'active';}?>" href="logcall.php"><img src="Icon.jpg" class="icon"></a></div>

<div class="topnav">

<a class="<?php if($page=='rylogcall'){echo 'active';}?>" href="logcall.php">Log Call</a>
		
<a class="<?php if($page=='ryupdate'){echo 'active';}?>" href="update.php">Update</a>
																	   
<a class="<?php if($page=='ryreport'){echo 'active';}?>" href="#">Report</a>
	
<a class="<?php if($page=='ryhistory'){echo 'active';}?>" href="#">History</a>
</div>

