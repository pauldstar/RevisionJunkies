<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QP_Email extends CI_Email
{
  /**
   * OVERRIDE
   * Get Hostname
   * Returns G Suite smtp auth settings title
   *
   * There are only two legal types of hostname - either a fully
   * qualified domain name (eg: "mail.example.com") or an IP literal
   * (eg: "[1.2.3.4]").
   *
   * @link	https://tools.ietf.org/html/rfc5321#section-2.3.5
   * @link	http://cbl.abuseat.org/namingproblems.html
   * @return	string
   */
  protected function _get_hostname()
  {
    // custom made hostname for QuePenny via Google Admin
    return 'stmp-auth.quepenny.com';
  }
}
