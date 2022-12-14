<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Group;
use Illuminate\Support\Facades\Cache;


class ContactController extends Controller
{
    protected $user_id;

    public function __construct() {
        $this->user_id = Cache::get('userId');
        // $this->user_id = 7;
    }

    // ======== Group Controller ========== //

    public function groupindex() {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');
        
        $data = Group::where('user_id', $this->user_id)->orderBy('created_at', 'desc')->get();
        foreach($data as $row) {
            $row['count'] = Contact::where('group_id', $row->id)->get()->count();
        }
        return view('groups.index', compact('data'));
    }

    public function groupcreate() {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        return view('groups.create');
    }

    public function groupstore(Request $request) {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        $groupCnt = Group::get()->count();
        if($groupCnt == 8)
            return redirect()->route('group.index')->with('error', 'You can create groups upto 8 maximually.');
            

        $new_group = [
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $this->user_id,
        ];
        Group::create($new_group);

        return redirect()->route('group.index')->with('success', 'Your group is successfully created.');
    }

    public function groupedit($id) {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        $group = Group::where('id', $id)->first();
        return view('groups.edit', compact('group'));
    }

    public function groupupdate(Request $request) {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        $edit_group = [
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $this->user_id,
        ];
        Group::where('id', $request->id)->update($edit_group);

        return redirect()->route('group.index')->with('success', 'Your group is successfully updated.');
    }

    public function groupdelete(Request $request) {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');
        
        Group::where('user_id', $this->user_id)->where('id', $request->id)->delete();
        Contact::where('group_id', $request->id)->delete();

        return redirect()->route('group.index')->with('success', 'Your group and contact(s) in it are successfully removed.');

    }

    // ======== Contact Controller ========= //

    //index view
    public function index($groupId) {
        $groups = Group::where('user_id', $this->user_id)->where('id', $groupId)->get();
        if(!$groups)
            return view('forbidden');

        $data = Contact::where('group_id', $groupId)->orderBy('created_at', 'desc')->get();
        return view('contacts.index', compact('data', 'groupId'));
    }

    public function create($groupId) {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        return view('contacts.create', compact('groupId'));
    }

    public function store(Request $request) {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        // $contacts = Contact::where('email', $request->email)->get();
        // if(count($contacts) != 0)
        //     return redirect()->back()->with('error', 'Email is already taken');

        $new_contact = [
            'user_id' => $this->user_id,
            'email' => $request->email,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'sms' => $request->sms,
            'whatsapp' => $request->whatsapp,
            'double_opt_in' => $request->double_opt_in,
            'opt_in' => $request->opt_in,
            'group_id' => $request->groupId,
        ];
        Contact::create($new_contact);

        return redirect()->route('contact.index', $request->groupId)->with('success', 'Your contact is successfully created.');
    }

    public function edit(Request $request, $id) {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        // If the contact is not assigned to current user, throw exception
        $result = Contact::where('id', $id)->first();
        if(!$result || $result->group->user_id != $this->user_id)
            return view('forbidden');

        // Else go to edit page
        $contact = Contact::where('id', $id)->first();
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request) {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        $edit_contact = [
            'email' => $request->email,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'sms' => $request->sms,
            'whatsapp' => $request->whatsapp,
            'double_opt_in' => $request->double_opt_in,
            'opt_in' => $request->opt_in,
        ];
        Contact::where('id', $request->id)->update($edit_contact);

        return redirect()->route('contact.index', $request->group_id)->with('success', 'Your contact is successfully updated.');
    }

    public function delete(Request $request) {
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        // If the contact is not assigned to current user, throw exception
        $result = Contact::where('group_id', $request->group_id)->where('id', $request->id)->first();
        if(!$result)
            return view('forbidden');

        Contact::where('id', $request->id)->delete();
        return redirect()->route('contact.index', $request->group_id)->with('success', 'It is successfully removed.');
    }

    public function deleteSelected(Request $request) {
        $selected = json_decode($request->selected);
        if(!$this->user_id)
            return redirect()->to(env('base_url'). '/?page_id=394');

        $result = Contact::where('group_id', $request->group_id)->whereIn('id', $selected)->first();
        
        if(!$result || $result->group->user_id != $this->user_id)
            return view('forbidden');

        Contact::whereIn('id', $selected)->delete();
        echo json_encode("success");
        // return redirect()->route('contact.index')->with('success', 'Selected Contat(s) successfully removed');
    }

    public function import($groupId) {
        return view('contacts.import', compact('groupId'));
    }

    public function fileimport(Request $request, $groupId) {
        $type = $request->type;
        $file = $request->file('file');
        if($file)
            $filename = time(). '_'. $file->getClientOriginalName();
        
        $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

        // == Throw exception === //
        // if($extension != 'xls' && $extension != 'xlsx' && $extension != 'txt')
        //     return;

        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize(); //Get size of uploaded file in bytes
        //Check for file extension and size
        // $this->checkUploadedFileProperties($extension, $fileSize);
        //Where uploaded file will be stored on the server 
        $location = 'uploads'; //Created an "uploads" folder for that
        // Upload file
        $file->move('public/'. $location, $filename);
        // In case the uploaded file path is to be stored in the database 
        $filepath = public_path($location . "/" . $filename);
        // Reading file
        $file = fopen($filepath, "r");


        $importData_arr = array(); // Read through the file and store the contents as an array
        $i = 0;
        //Read the contents of the uploaded file 
        while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
            $num = count($filedata);
            // Skip first row (Remove below comment if you want to skip the first row)
            if ($i == 0) {
                if(!($type == 'hybrid' && $num == 7) && !($type == 'google' && $num == 31))
                    return redirect()->back()->with('error', 'The imported file is invalid. Please check sample templates regarding to type.');
                $i++;
                continue;
            }
            for ($c = 0; $c < $num; $c++) {
                $importData_arr[$i][] = $filedata[$c];
            }
            $i++;
        }
        return view('contacts.import-process', ['data' => $importData_arr, 'filename' => $filename, 'type' => $type, 'groupId' => $groupId]);
    }

    public function upload(Request $request, $groupId) {
        $filename = $request->filename;
        $type = $request->type;

        $location = 'uploads'; //Created an "uploads" folder for that
        $filepath = public_path($location . "/" . $filename);
        // Reading file
        $file = fopen($filepath, "r");


        $importData_arr = array(); // Read through the file and store the contents as an array
        $i = 0;
        //Read the contents of the uploaded file 
        while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
            $num = count($filedata);
            // Skip first row (Remove below comment if you want to skip the first row)
            if ($i == 0) {
                $i++;
                continue;
            }
            
            if($filedata[0] != '') {
                if($type == 'hybrid') {
                    $new_contact = [
                        'group_id' => $groupId,
                        'email' => $filedata[0],
                        'lastname' => isset($filedata[1]) ? $filedata[1] : '',
                        'firstname' => isset($filedata[2]) ? $filedata[2] : '',
                        'sms' => isset($filedata[3]) ? $filedata[3] : '',
                        'whatsapp' => isset($filedata[4]) ? $filedata[4] : '',
                        'double_opt_in' => isset($filedata[5]) ? $filedata[5] : '',
                        'opt_in' => isset($filedata[6]) ? $filedata[6] : '',
                    ];
                } else if($type == 'google') {
                    $new_contact = [
                        'group_id' => $groupId,
                        'email' => $filedata[30],
                        'lastname' => isset($filedata[1]) ? $filedata[1] : '',
                        'firstname' => isset($filedata[3]) ? $filedata[3] : '',
                        'sms' => '',
                        'whatsapp' => '',
                        'double_opt_in' => '',
                        'opt_in' => '',
                    ];
                }
                
                Contact::create($new_contact);
            }
        
            $i++;
        }
        return redirect()->route('contact.index', $groupId)->with('success', 'Your contact is imported successfuly.');
    }
}
