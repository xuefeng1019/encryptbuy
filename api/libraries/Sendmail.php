<?php
/**
 * 发送邮件
 */
class Mail_Sendmail
{
    public function Mail_Sendmail() {
    }

	static public function sendmail($to, $from, $fromdesc, $subject, $plaintext, $content) {
		static $phpmailer = NULL;

		if ( $phpmailer == NULL ) {
			$phpmailer = new Mail_PHPMailer();
		}

		if ( !is_array($to) ) {
			$to = array($to);
		}

		try {
		  $phpmailer->CharSet = "UTF-8";
		  //$phpmailer->IsSMTP();
		  $phpmailer->IsSendmail();
		  //$phpmailer->SMTPAuth = false;
		  //$phpmailer->Host = '121.14.48.194';
		  $phpmailer->SetFrom($from, "=?UTF-8?B?".base64_encode($fromdesc)."?=");
		  foreach ( $to as $dest ) {
			$destname = @ explode('@', $dest);
			$destname = $destname[0];
			$phpmailer->AddAddress($dest, "=?UTF-8?B?".base64_encode($destname)."?=");
		  }
		  $phpmailer->Subject = "=?UTF-8?B?".base64_encode($subject)."?=";
		  $phpmailer->AltBody = $plaintext;
		  $phpmailer->MsgHTML($content);
		  $phpmailer->Send();
		  return TRUE;
		} catch (phpmailerException $e) {
		  return FALSE;
		} catch (Exception $e) {
		  return FALSE;
		}

		return TRUE;
	}
}
