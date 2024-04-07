<?php

namespace App\Eventbuizz\Repositories;

use App\Models\Organizer;
use Illuminate\Http\Request;

class OrganizerRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function getOrganizerPermissionsModule($module_alies, $action)
    {
        $user = organizer_info();
        $module_id = \App\Models\OrganizerPermission::where('module_name', $module_alies)->value("id");

        if ($user->user_type == 'super' || $user->user_type == 'demo') {
            $status = '1';
            return $status;
        } elseif ($user->user_type == 'admin') {
            if ($module_alies == 'readonly') {
                $status = '1';
            }

            $permision = \App\Models\OrganizerUserPermission::where('organizer_user_id', $user->id)->where('permission_id', $module_id)->first();

            if ($permision->add_permissions == '1' && $action == 'add') {
                $status = '1';
            } elseif ($permision->edit_permissions == '1' && $action == 'edit') {
                $status = '1';
            } elseif ($permision->delete_permissions == '1' && $action == 'delete') {
                $status = '1';
            } elseif ($permision->view_permissions == '1' && $action == 'view') {
                $status = '1';
            } else {
                $status = '0';
            }

            return $status;
        } elseif ($user->user_type == 'readonly') {

            if ($action == 'add') {
                $status = '0';
                return $status;
            } elseif ($action == 'edit') {
                $status = '0';
                return $status;
            } elseif ($action == 'delete') {
                $status = '0';
                return $status;
            } elseif ($action == 'view') {
                $status = '1';
                return $status;
            }
        }
    }

    /**
     * fetch logged organizer
     */

    public function getOrganizer()
    {
        $organizer = request()->user();
        $phone = explode("-", $organizer->phone);
        if (count($phone) > 1) {
            $organizer->code = $phone[0];
            $organizer->phone = $phone[1];
        }
        return $organizer;
    }

    /**
     * Fetch organizer by ID
     * @param $id
     * @return object
    */
    public static function getOrganizerById($id)
    {
        return \App\Models\Organizer::where('id', $id)->first();
    }

    /**
     * Set form values for update organizer
     *
     * @param array
     */

    public function setForm($formInput)
    {
        if ($formInput['phone']) {
            $formInput['phone'] = $formInput['code'] . '-' . $formInput['phone'];
        }
        $this->setFormInput($formInput);
        return $this;
    }

    /**
     * update organizer
     *
     * @param array
     * @param object
     */

    public function edit($formInput, $organizer)
    {
        $instance = $this->setForm($formInput);
        $instance->update($organizer);
    }

    /**
     * Set form values for update orgnaizer password
     *
     * @param array
     */

    public function setChangePasswordForm($formInput)
    {
        $formInput['password'] = bcrypt($formInput['password']);
        $this->setFormInput($formInput);
        return $this;
    }

    /**
     * update organizer password
     *
     * @param array
     * @param object
     */

    public function change_password($formInput, $organizer)
    {
        $instance = $this->setChangePasswordForm($formInput);
        $instance->update($organizer);
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function zoomCredentials($formInput)
    {
        return \App\Models\OrganizerIntegrationCredential::where('organizer_id', $formInput['organizer_id'])->first();
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function fetchOrganizer($formInput)
    {
        $organizer = \App\Models\Organizer::where('id', $formInput['organizer_id'])->first();
        return $organizer;
    }

 /**
  * @param mixed $formInput
  * 
  * @return [type]
  */
    static public function getPackages($formInput)
    {
        $packages = array();

        $current_date = date('Y-m-d');

        $packageModel = \App\Models\Organizer::find($formInput['organizer_id'])
            ->packages()
            ->where('package_expire_date', '>=', $current_date)
            ->orWhere('package_expire_date', '0000-00-00')
            ->get()
            ->toArray();

        foreach ($packageModel as $package) {
            $current_date_time = date('Y-m-d H:i:s');

            $assign_packages = \App\Models\AssignPackage::where('organizer_id', $package['pivot']['organizer_id'])->where('package_id', $package['pivot']['package_id'])->where('package_expire_date', '>=', $current_date_time)->get()->toArray();

            foreach($assign_packages as $assigned_package) {
                if (strtolower($package['no_of_event']) == 'unlimited') {
                    $packages[$assigned_package['id']] = $package['name'] . ' ( #' . $assigned_package['id'] . ' )';
                } else {
                    $total_created = \App\Models\AssignPackageUsed::where('assign_package_id', $assigned_package['id'])->count();
                    if ($total_created < 1) {
                        $packages[$assigned_package['id']] = $package['name'] . ' ( #' . $assigned_package['id'] . ' )';
                    }
                }
            }
        }

        return $packages;
    }
}
