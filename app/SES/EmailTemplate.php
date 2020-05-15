<?php

namespace App\SES;

class EmailTemplate
{
  public static function getPasswordResetTemplate($vars)
  {
    $template =  '
    <div style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;background-color:#f5f8fa;color:#74787e;height:100%;line-height:1.4;margin:0;width:100%!important;word-break:break-word">
      <table width="100%" cellpadding="0" cellspacing="0" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;background-color:#f5f8fa;margin:0;padding:0;width:100%"><tbody><tr>
        <td align="center" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box">
          <table width="100%" cellpadding="0" cellspacing="0" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;margin:0;padding:0;width:100%">
            <tbody>
              <tr>
                <td style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;padding:25px 0;text-align:center">
                  <a href="http://email.clientDomain.thedevserver.io/c/eJwNzUEOgyAQAMDXyJEgC4gHDr30Hwu7KAlqA8Smv6_J3IcCRnAgStBpJSCnXfS4enDGR_Kw5JSNZ2XUZFT_ljzkxic3HExiD0sGNnkxsCo1o-bZW2Mh5WwpoXVO1LCP8ekTvCb9fqRa-Bxcqxw7E9-d281Nlku00K4Dz17aj55rO7BUma7jD17KM14" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;color:#bbbfc3;font-size:19px;font-weight:bold;text-decoration:none" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://email.clientDomain.thedevserver.io/c/eJwNzUEOgyAQAMDXyJEgC4gHDr30Hwu7KAlqA8Smv6_J3IcCRnAgStBpJSCnXfS4enDGR_Kw5JSNZ2XUZFT_ljzkxic3HExiD0sGNnkxsCo1o-bZW2Mh5WwpoXVO1LCP8ekTvCb9fqRa-Bxcqxw7E9-d281Nlku00K4Dz17aj55rO7BUma7jD17KM14&amp;source=gmail&amp;ust=1584002176767000&amp;usg=AFQjCNH8oV1pc1OoQ_M47LvC6e_B9kp1LA">
                      clientDomain
                  </a>
                </td>
              </tr>
              <tr>
                <td width="100%" cellpadding="0" cellspacing="0" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;background-color:#ffffff;border-bottom:1px solid #edeff2;border-top:1px solid #edeff2;margin:0;padding:0;width:100%">
                  <table align="center" width="570" cellpadding="0" cellspacing="0" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;background-color:#ffffff;margin:0 auto;padding:0;width:570px">
                    <tbody>
                      <tr>
                        <td style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;padding:35px">
                          <span class="im">
                            <h1 style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;color:#2f3133;font-size:19px;font-weight:bold;margin-top:0;text-align:left">Hello!</h1>
                            <p style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;color:#74787e;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">Enter the token to the App or click the link to reset your password</p>
                          </span>
                          <p style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;color:#74787e;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">Token: $token</p>
                          <table align="center" width="100%" cellpadding="0" cellspacing="0" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;margin:30px auto;padding:0;text-align:center;width:100%">
                            <tbody>
                              <tr>
                                <td align="center" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box">
                                  <table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box">
                                    <tbody>
                                      <tr>
                                        <td align="center" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box">
                                          <table border="0" cellpadding="0" cellspacing="0" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box">
                                            <tbody>
                                              <tr>
                                                <td style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box">
                                                  <a href="$url" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;border-radius:3px;color:#fff;display:inline-block;text-decoration:none;background-color:#3097d1;border-top:10px solid #3097d1;border-right:18px solid #3097d1;border-bottom:10px solid #3097d1;border-left:18px solid #3097d1" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://email.clientDomain.thedevserver.io/c/eJxNyT1uxCAQQOHT4C4WYTA_BUWkaIsoXU4weMZrFGw2gGzl9nG6lV71PgoYwcCQgpo9ARllokPvwGgXyYFd5kU7lloKLduZlj7eeeeKnWlYg7HReuVUnCxNcWGlYzRWojdovQQcclh7fzQBb0LdruaceO-c89hXJj4a14PrmMpllRv3lwe2dpZKAm69fPMu4P2Ez4_j9etHKMMbpnytWjbcW6q_JNSk5f1_j3PZhhqe6An-AOUdS6k&amp;source=gmail&amp;ust=1584002176767000&amp;usg=AFQjCNHD3oPy4T0WosmbixaKOKgvf7kmyQ">Reset password</a>
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                          <p style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;color:#74787e;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">Regards,<br>clientDomain</p>
                          <table width="100%" cellpadding="0" cellspacing="0" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;border-top:1px solid #edeff2;margin-top:25px;padding-top:25px"><tbody><tr>
                            <td style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box">
                                        <p style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;color:#74787e;line-height:1.5em;margin-top:0;text-align:left;font-size:12px">If you’re having trouble clicking the "Reset password" button, copy and paste the URL below
                            into your web browser: <a href="$url" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;color:#3869d4" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://email.clientDomain.thedevserver.io/c/eJxNyT1uxCAQQOHT4C4WYTA_BUWkaIsoXU4weMZrFGw2gGzl9nG6lV71PgoYwcCQgpo9ARllokPvwGgXyYFd5kU7lloKLduZlj7eeeeKnWlYg7HReuVUnCxNcWGlYzRWojdovQQcclh7fzQBb0LdruaceO-c89hXJj4a14PrmMpllRv3lwe2dpZKAm69fPMu4P2Ez4_j9etHKMMbpnytWjbcW6q_JNSk5f1_j3PZhhqe6An-AOUdS6k&amp;source=gmail&amp;ust=1584002176767000&amp;usg=AFQjCNHD3oPy4T0WosmbixaKOKgvf7kmyQ">$url</a></p>
                                    </td>
                                </tr></tbody></table>
                            </td>
                                                            </tr>
                            </tbody>
                          </table>
                          </td>
                                    </tr>
                <tr>
                <td style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box">
                        <table align="center" width="570" cellpadding="0" cellspacing="0" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;margin:0 auto;padding:0;text-align:center;width:570px"><tbody><tr>
                <td align="center" style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;padding:35px">
                                    <p style="font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;line-height:1.5em;margin-top:0;color:#aeaeae;font-size:12px;text-align:center">© 2020 clientDomain. All rights reserved.</p>
                                </td>
                            </tr></tbody></table>
                </td>
                </tr>
                </tbody></table>
                </td>
            </tr></tbody></table>
          <img width="1px" height="1px" alt="" src="https://ci3.googleusercontent.com/proxy/jAbnHZtarUDKxzqWkY61rCmxsYZQKxncy6fyPt-6Wv6PYptTHp22vsQHGl3Q6HIy5GCH7YVkYgg8AVoo3QldB7KoFDZIv3Ahz991J22hTA7pl7NZS574wYtDZkiBSfdGRyczRA2aZ2tX6WXE1mDQrwIDqWrN4Sf4zazTroBWWwUE2ejTJc2K-LgnpcjqDF3vISpNMlxuiVozTzZtTwOegwYMgywobU4PNAlX-jCttbfJyzOUI5D208xcIlbu90Jzh7TZcXxC73zyYE5vjqZRLg=s0-d-e1-ft#http://email.clientDomain.thedevserver.io/o/eJwNzFEOgyAMANDTjE-DtJbywWEKLY5ENEGTZbff3gGeZilA4HoONSkoBSosiYGQizLEVhuyefQv9Pent2fZ7bQpj6l7ZyZbWwplk4jma4zAYoFJtkZJ1dzM8xpy3n1-9T_sQ_qx1Gv8AFELJPM" class="CToWUd"></div>';
    $messageContent = '
      <p>Enter the token to the App or click the link to reset your password test</p> 
      <p>Token: $token</p>
      <p><a href="$url">Reset password</a> </p>';
    return strtr($template, $vars);
  }
}
