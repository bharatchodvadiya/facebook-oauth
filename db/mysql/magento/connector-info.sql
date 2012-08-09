-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 28, 2012 at 06:21 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `magento`
--
use magento;
-- --------------------------------------------------------

--
-- Table structure for table `sohyper_event_connectors`
--

DELETE FROM `sohyper_event_connectors` where connectorid=2;
INSERT INTO `sohyper_event_connectors` (`connectorid`, `connectorname`, `api_key`, `api_secret_key`, `connectorimage`, `api_active`) VALUES
(2, 'Facebook', '193194090693725', '4ed3e5207d1868ab2952bde614e951eb', 'facebook.jpg', 'yes');

-- --------------------------------------------------------
-- facebook register : send email to user 
-- --------------------------------------------------------

DELETE FROM `magento_core_email_template` where template_id=2 ;

INSERT INTO `magento_core_email_template` (`template_id`, `template_code`, `template_text`, `template_styles`, `template_type`, `template_subject`, `template_sender_name`, `template_sender_email`, `added_at`, `modified_at`, `orig_template_code`, `orig_template_variables`) VALUES
(2, 'facebook signin', '<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">\r\n<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">\r\n<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">\r\n        <tr>\r\n            <td align="center" valign="top" style="padding:20px 0 20px 0">\r\n                <!-- [ header starts here] -->\r\n                <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">\r\n                    <tr>\r\n                        <td valign="top">\r\n                            <a href="{{store url=""}}"><img src="{{skin url="images/logo_email.gif" _area=''frontend''}}" alt="{{var store.getFrontendName()}}"  style="margin-bottom:10px;" border="0"/></a></td>\r\n                    </tr>\r\n                <!-- [ middle starts here] -->\r\n                    <tr>\r\n                        <td valign="top">\r\n                            <h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;"">Dear {{htmlescape var=$customer.name}},</h1>\r\n                            <p style="font-size:12px; line-height:16px; margin:0 0 16px 0;">Welcome to {{var store.getFrontendName()}}. To log in when visiting our site just click <a href="{{store url="customer/account/"}}" style="color:#1E7EC8;">Login</a> or <a href="{{store url="customer/account/"}}" style="color:#1E7EC8;">My Account</a> at the top of every page, and then enter your e-mail address and password.</p>\r\n                            <p style="border:1px solid #E0E0E0; font-size:12px; line-height:16px; margin:0; padding:13px 18px; background:#f9f9f9;">\r\n                                Use the following values when prompted to log in:<br/>\r\n                                <strong>E-mail</strong>: {{var customer.email}}<br/>\r\n                                <strong>Password</strong>: {{htmlescape var=$customer.password}}<p>\r\n                            <p style="font-size:12px; line-height:16px; margin:0 0 8px 0;">When you log in to your account, you will be able to do the following:</p>\r\n                            <ul style="font-size:12px; line-height:16px; margin:0 0 16px 0; padding:0;">\r\n                                <li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Proceed through checkout faster when making a purchase</li>\r\n                                <li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Check the status of orders</li>\r\n                                <li style="list-style:none inside; padding:0 0 0 10px;">&ndash; View past orders</li>\r\n                                <li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Make changes to your account information</li>\r\n                                <li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Change your password</li>\r\n                                <li style="list-style:none inside; padding:0 0 0 10px;">&ndash; Store alternative addresses (for shipping to multiple family members and friends!)</li>\r\n                            </ul>\r\n                            <p style="font-size:12px; line-height:16px; margin:0;">If you have any questions about your account or any other matter, please feel free to contact us at <a href="mailto:{{config path=''trans_email/ident_support/email''}}" style="color:#1E7EC8;">{{config path=''trans_email/ident_support/email''}}</a> or by phone at {{config path=''general/store_information/phone''}}.</p>\r\n                        </td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">Thank you again, <strong>{{var store.getFrontendName()}}</strong></p></center></td>\r\n                    </tr>\r\n                </table>\r\n            </td>\r\n        </tr>\r\n    </table>\r\n</div>\r\n</body>', 'body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }', 2, 'Welcome, {{var customer.name}} Signing in via facebook login !', NULL, NULL, NULL, '2012-06-30 03:53:15', 'customer_create_account_email_template', '{"store url=\\"\\"":"Store Url","skin url=\\"images/logo_email.gif\\" _area=''frontend''":"Email Logo Image","htmlescape var=$customer.name":"Customer Name","store url=\\"customer/account/\\"":"Customer Account Url","var customer.email":"Customer Email","htmlescape var=$customer.password":"Customer Password"}');