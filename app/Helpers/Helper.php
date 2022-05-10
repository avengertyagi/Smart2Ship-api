<?php

namespace App\Helpers;

use Exception;

class Helper
{
	public static function response($request)
	{
		$message['ok'] = 'Ok';
		$message['mobile_alredy'] = 'The mobile number has already taken';
		$message['unauthorized'] = 'Unauthorized';
		$message['register_success'] = 'User register successfully';
		$message['block_admin'] = 'your account has been block.Please contact to administrator';
		$message['login_success'] = 'User login successfully';
		$message['password_match'] = 'Password does not match';
		$message['not_registerd'] = 'User does not exit';
		$message['email_exit'] = 'Email does not exit';
		$message['old_password'] = 'your old password does not match';
		$message['bed_request'] = 'BadRequest';
		$message['password_change'] = 'User password changed successfully';
		$message['session_expire'] = 'Session expired';
		$message['logout_success'] = 'User logout successfully';
		$message['session_expire'] = 'session expire';
		$message['mail_sent']      = 'Mail has been send to your mail id';
		$message['user_fetch_list'] = 'User Fetchlist successfully';
		$message['user_profile_update'] = 'User Profile update successfully';
		$message['user_delete'] = 'Delete user successfuly';
		$message['user_multiple_list'] = 'User Fetchlist successfully';
		$message['not_found'] = 'Data not found';
		$message['something_went'] = 'something went wrong. Please try again';
		$message['password_reset_success'] = 'Password reset successfully';
		$message['verified_success'] = 'OTP verify Successfully';
		$message['otp_verified_not'] = 'Your OTP not verified, Please try again.';
		$message['debitcard_success'] = 'Card added successfuly.';
		$message['debitcard_makeprimary'] = 'Card primary successfuly';
		$message['user_card_delete']  = 'Card delete successfuly';
		$message['email_alredy']= 'Email already exits.';
		$message['user_address_success'] = 'User add address successfuly';
		$message['user_address_update_success'] = 'User update address successfuly';
		$message['addresscard_makeprimary']   = 'User address primary successfuly';
		$message['addresscard_delete']   = 'Address card delete successfuly';
		$message['company_success'] = 'Company added successfuly';
		$message['company_update_success'] = 'Company update successfully';
		$message['quote_success'] = 'Get Instant Quotes Successfuly';
		$message['quick_list'] = 'Quick Book Fetch List succcessfuly';
		$message['cartlist_success'] = 'My Cart Fetch list successfuly';
		return $message[$request];
	}
	public static function statusCode($key)
	{
		$date['bed_request'] = 400;
		$date['session_expire'] = 402;
		$date['ok'] = 200;
		$date['unauthorized'] = 401;
		$date['expectation_failed'] = 417;
		$date['bed_gate_way'] = 502;
		return $date[$key];
	}
	public static function SendMail($data, $otp)
	{
		$message = '<!DOCTYPE html>
		<html lang="en">
		<head>
		  <meta charset="utf-8">
		  <meta name="viewport" content="width=device-width,initial-scale=1">
		  <meta name="x-apple-disable-message-reformatting">
		  <title></title>
		  <style>
			table, td, div, h1, p {
			  font-family: Arial, sans-serif;
			}
			@media screen and (max-width: 530px) {
			  .unsub {
				display: block;
				padding: 8px;
				margin-top: 14px;
				border-radius: 6px;
				background-color: #555555;
				text-decoration: none !important;
				font-weight: bold;
			  }
			  .col-lge {
				max-width: 100% !important;
			  }
			}
			@media screen and (min-width: 531px) {
			  .col-sml {
				max-width: 27% !important;
			  }
			  .col-lge {
				max-width: 73% !important;
			  }
			}
		  </style>
		</head>
		<body style="margin:0;padding:0;word-spacing:normal;background-color:#f5f5f5;">
		  <div role="article" aria-roledescription="email" lang="en" style="text-size-adjust:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;background-color:#f5f5f5;">
			<table role="presentation" style="width:100%;border:none;border-spacing:0;">
			  <tr>
				<td align="center" style="padding:0;">
				  <table role="presentation" style="width:94%;max-width:500px;border:none;border-spacing:0;text-align:left;font-family:Arial,sans-serif;font-size:16px;line-height:22px;color:#363636;">
					<tr>
					  <td style="padding:30px 20px;text-align:center;font-size:24px;font-weight:bold; background-color: #6e6b6b;">
						<a href="/" style="text-decoration:none;"><img src="https://smart2ship.com/images/logo.png" width="165" alt="Logo" style="width:100px;max-width:80%;height:auto;border:none;text-decoration:none;color:#ffffff;"></a>
					  </td>
					</tr>
					<tr><td style="background-color: #fff;padding: 20px 0;"></td></tr>
					<tr>
					  <td style="padding:30px;background-color:#d7d38f;">
						<h1 style="margin-top:0;margin-bottom:16px;font-size:20px;line-height:32px;font-weight:bold;letter-spacing:-0.02em;">Hello  ' . $data['email'] . ',</h1>
						<p style="margin:0; font-weight: 500;">You are receiving this email because we received a otp for your account.' . $otp . '</p>
					  </td>
					</tr>
					<tr><td style="background-color: #fff;padding: 20px 0;"></td></tr>
					<tr>
					  <td style="padding:30px;text-align:center;font-size:12px;background-color:#1b1d50;color:#fff;">
						<p style="margin:0;font-size:14px;line-height:20px;">2021 &#169; All Right Reserved</p>
					  </td>
					</tr>
				  </table>
				</td>
			  </tr>
			</table>
		  </div>
		</body>
		</html>';
		include  public_path() . '/third_party/sendgrid/sendgrid-php.php';
		$email = new \SendGrid\Mail\Mail();
		$subject = 'Smart2Ship: Reset Password';
		$email->setfrom("developer@smart2ship.com");
		$email->setSubject($subject);
		$email->addTo($data['email']);
		$email->addContent("text/plain", "subject");
		$email->addContent("text/html", $message);
		$sendgrid = new \SendGrid('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');
		try {
			$response = $sendgrid->send($email);
			return 1;
		} catch (Exception $e) {
			return 0;
		}
	}
}
