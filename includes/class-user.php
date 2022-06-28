<?php

namespace Payamito\Edd;

/**
 *  Class Payamito_Edd_User 
 *
 * @package Payamito
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

if (!class_exists("User")) {

    class User
    {
        /**
         * The single instance of the class.
         *
         * @since 1.1.2
         */

        protected static $instance = null;

        public static function get_instance()
        {
            if (is_null(self::$instance) && !(self::$instance instanceof User)) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Active users and payment statuses are stored in this variable
         *
         * @var Payamito_Edd
         * @since 1.0
         */
        public  $ty_active_users = array(
            "administrator" => array(
                "active" => false,
                "publish" => false,
                "pending" => false,
                "processing" => false,
                "refunded" => false,
                "revoked" => false,
                "failed" => false,
                "abandoned" => false,
            ), "customer" => array(
                "active" => false,
                "publish" => false,
                "pending" => false,
                "processing" => false,
                "refunded" => false,
                "revoked" => false,
                "failed" => false,
                "abandoned" => false,
            ), "vendor" => array(
                "active" => false,
                "publish" => false,
                "pending" => false,
                "processing" => false,
                "refunded" => false,
                "revoked" => false,
                "failed" => false,
                "abandoned" => false,
            )
        );

        /**
         *Check active status
         *Payment statuses activated by the admin panel in this function to send
         *SMS are prepared
         *@param Not param
         * @since 1.0
         * @return 
         */
        public function status()
        {
            global $edd_payamito_messages_options;

            $tys = array("administrator", "customer", "vendor");

            if (is_array($edd_payamito_messages_options)) {

                foreach ($edd_payamito_messages_options as $index => $op) {

                    foreach ($tys as $ty) {

                        $active = $ty . "_sms_active";

                        if (($active) == $index && $op == "1") {

                            if ($ty == "administrator" && $edd_payamito_messages_options["administrator_sms_active"] == "1") {
                                $this->ty_active_users["administrator"]["active"] = true;

                                $this->check_status($ty);
                            }
                            if ($ty == "customer" &&  $edd_payamito_messages_options["customer_sms_active"] == "1") {

                                $this->ty_active_users["customer"]["active"] = true;

                                $this->check_status($ty);
                            }
                            if ($ty == "vendor" &&  $edd_payamito_messages_options["vendor_sms_active"] == "1") {

                                $this->ty_active_users["vendor"]["active"] = true;

                                $this->check_status($ty);
                            }
                        }
                    }
                }
            }

            return  $this->ty_active_users;
        }

        /**
         *Check active status
         *@param Not param
         * @since 1.0
         * @return 
         */
        public function check_status($type)
        {
            global $edd_payamito_messages_options;

            $status = payamito_edd()->functions::status();

            foreach ($status as $st) {

                if (($edd_payamito_messages_options[$type . "_" . $st . "_payment_active"]) == '1') {
                    switch ($st) {

                        case "publish":
                            $this->ty_active_users[$type][$st] = true;
                            break;

                        case "pending":
                            $this->ty_active_users[$type][$st] = true;
                            break;

                        case "processing":
                            $this->ty_active_users[$type][$st] = true;
                            break;

                        case "refunded":
                            $this->ty_active_users[$type][$st] = true;
                            break;

                        case "revoked":
                            $this->ty_active_users[$type][$st] = true;
                            break;

                        case "failed":
                            $this->ty_active_users[$type][$st] = true;
                            break;

                        case "abandoned":
                            $this->ty_active_users[$type][$st] = true;
                            break;
                    }
                }
            }
        }

        /**
         *getting admin phone number
         *@param Not param
         * @since 1.0
         * @return 
         */
        public function admin_mobile()
        {
            global $edd_payamito_messages_options;

            $mobiles = $edd_payamito_messages_options['admin_phone_number_repeater'];

            $send_mobile = [];

            if (!is_array($mobiles) || count($mobiles) <= 0) {

                return [];
            } else {
                foreach ($mobiles as $mobile) {

                    array_push($send_mobile, $mobile['admin_phone_number']);
                }
            }
            return  $send_mobile;
        }

        /**
         *getting vendor phone number
         *@param Not param
         * @since 1.0
         * @return 
         */
        public function vendor_mobile(array $users)
        {
            global $edd_payamito_messages_options;

            $mobiles = array();

            $meta_key = $edd_payamito_messages_options["vendor_meta_key"];

            if (!is_null($meta_key)) {

                foreach ($users as $u) {

                    $result = get_user_meta($u["id"], $meta_key, true);

                    if ($result !== false && !empty($result)) {

                        array_push($mobiles, $result);
                    }
                }
            }

            if (count($result) == 0) {

                foreach ($users as $u) {

                    $result = get_user_meta($u["id"], "edd_payamito_mobile", true);

                    if ($result !== false && !empty($result)) {

                        array_push($mobiles, $result);
                    }
                }
            }
            if (count($mobiles) > 1) {

                return $mobiles;
            }

            return  $mobiles;
        }

        /**
         *getting customer phone number
         *@param Not param
         * @since 1.0
         * @return 
         */
        public function customer_mobile(string $id)
        {
            global $edd_payamito_messages_options;

            $meta_key = $edd_payamito_messages_options["customer_meta_key"];

            $mobile = null;

            if (!is_null($meta_key)) {

                $result = get_user_meta($id, $meta_key, true);
            }
            if ($result !== false && !empty($result)) {

                return $mobile = $result;
            }
            if ($result == false || $result == "") {

                $result = get_user_meta($id, "edd_payamito_mobile", true);

                if ($result !== false && !empty($result)) {

                    return  $result;
                }
            }

            return  $mobile;
        }
    }
}
