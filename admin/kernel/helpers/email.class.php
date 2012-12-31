<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 29/12/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Email
{
	private $headers;
	private $subject;
	private $message;
	private $to;

	function EMAIL()
	{
		$this->headers = 'MIME-Version: 1.0' . "\r\n";
		$this->headers.= 'Content-type: text/html; charset=utf-8' . "\r\n";
	}

	public function set_from($from_name, $from_mail)
	{
		$this->headers = 'From: '.$from_name.' <'.$from_mail.'>' . "\r\n";
		$this->headers.= 'Reply-To: '.$from_name.' <'.$from_mail.'>' . "\r\n";
	}

	public function set_to($to)
	{
		$this->to = $to;
	}

	public function set_subject($subject)
	{
		$this->subject = $subject;
	}

	public function set_message($text)
	{
		$this->message = $text;
	}

	public function send()
	{
		return( mail($this->to, $this->subject, $this->message, $this->headers) );
	}

}

?>