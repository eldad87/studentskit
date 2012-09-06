<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Adaptive Payment - Payment Details</title>
<link href="Common/sdk.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="Common/sdk_functions.js"></script>
<script type="text/javascript" src="Common/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="Common/jquery.qtip-1.0.0-rc3.min.js"></script>
<script type="text/javascript">
		toolTips = {
			payKey : "The pay key that identifies the payment for which you want to retrieve details. <br />This is the pay key returned in the PayResponse message",
		}	
		$(document).ready( function () {
			jQuery.each(toolTips, function(id, toolTip) {
				$("#"+id).attr("title", toolTip);
			}); 
			$("input[title]").qtip(qtipConfig);
			$("select[title]").qtip(qtipConfig);
		});
	</script>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<h3>Payment Details</h3>
			<div id="apidetails">The request to look up the details of a
				PayRequest. The PaymentDetailsRequest can be made with either a
				payKey, trackingId, or a transactionId of the PayRequest.</div>
		</div>
		<div id="request_form">
			<form id="Form1" name="Form1" method="post"
				action="PaymentDetailsReceipt.php">
				<div class="params">
					<div class="param_name">Pay key</div>
					<div class="param_value">
						<input name="payKey" id="payKey" value="AP-5S482348KH512131U" />
					</div>
				</div>
				<div class="params">
					<div class="param_name">Transaction Id</div>
					<div class="param_value">
						<input name="transactionId" id="transactionId" value="" />
					</div>
				</div>
				<div class="params">
					<div class="param_name">Tracking Id</div>
					<div class="param_value">
						<input name="trackingId" id="trackingId" value="" />
					</div>
				</div>
				<div class="submit">
					<input type="submit" value="Submit" />
				</div>
			</form>
		</div>
	</div>
</body>
</html>
