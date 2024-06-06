<?php namespace App\Controllers;
 
use CodeIgniter\RESTful\ResourceController;

use App\Models\Symptoms_model;
use App\Models\System_model;
 
class Symptoms extends ResourceController
{
    public function __construct()
    {
        $this->symptom = new Symptoms_model();
        $this->sysModel = new System_model();
    }

    public function index()
    {
        $config = [
            'start' => empty($this->request->getGet('start')) ? 0 : (int)$this->request->getGet('start'),
            'length' => empty($this->request->getGet('length')) ? 0 : (int)$this->request->getGet('length'),
            'order_column' => empty($this->request->getGet('order_column')) ? null : $this->request->getGet('order_column'),
            'order_dir' => empty($this->request->getGet('order_dir')) ? null : $this->request->getGet('order_dir'),
            'search' => empty($this->request->getGet('search')) ? null : urldecode($this->request->getGet('search')),
        ];

		$config_count = [
            'start' 		=> null,
            'length' 		=> null,
            'order_column' 	=> null,
            'order_dir'		=> null,
            'search' 		=> null,
        ];

        $dataSymptoms = [];

		$getCountTotal 	        = $this->symptom->getSymptomData($config_count, true);
        $getSymptomData		    = $this->symptom->getSymptomData($config);

        foreach ($getSymptomData as $key => $value) {
            
            $dataSymptomsDetails        = [];
            $getSymptomDetail		    = $this->symptom->get_symptom_detail_byid($value->id);

            $dataSymptoms[] = [
                'id'                    => $value->id,
                'code'                  => $value->code,
                'name'                  => $value->name,
                'suggestion'            => $value->suggestion,
                'created_at'            => $value->created_at,
                'created_by'            => $value->created_by,
                'symptom_detail_data'   => $getSymptomDetail,
            ];
        }

        $response = [
	        'status' => 200,
			'message' => 'success',
            'count' => ( $dataSymptoms !== 0 ? count($dataSymptoms) : 0),
            'count_total' => ( $getCountTotal !== 0 ? (int)$getCountTotal : 0),
            'data' => $dataSymptoms,
        ];

        return $this->respond($response, 200);
    }

    public function getID()
    {
        return $data = $this->sysModel->getUUID()->id;
    }

    public function getDataSymptoms()
    {
        $symptoms = $this->symptom->get_symptoms();
        return $this->respond($symptoms, 200);
    }

    public function getDataCount()
    {
        $symptom = $this->symptom->get_count();
        return $this->respond($symptom, 200);
    }
 
 
    public function add_symptoms()
    {
        $code = $this->request->getVar('code');
        $name  = $this->request->getVar('name');
        $userId = 'qwerty123';
        $suggest = $this->request->getVar('suggestion');

        $character = "batuk berdahak";
        $suggestCharacter = "seugges";

        $getUUID = $this->getID();
 
        $dataSymptoms = [
            'id'                => $getUUID,
            'code'              => $code,
            'name'              => $name,
            'suggestion'        => $suggest,
            'created_at'        => date("Y-m-d H:i:s"),
            'created_by'        => $userId,
            'deleted'           => '0',
        ];

        $dataCharacterFromFe = $this->request->getVar('character');

        $dataCharacter = [];

        foreach($dataCharacterFromFe as $dataValue) {
            $data = [
                'id'                => $this->getID(),
                'symptom_id'        => $getUUID,
                'name'              => $dataValue->karakter,
                'suggestion'        => $dataValue->saranKarakter,
                'created_at'        => date("Y-m-d H:i:s"),
                'created_by'        => $userId,
                'deleted'           => '0',
            ];
            array_push($dataCharacter, $data);
        }
        
        $symptoms = $this->symptom->create_symptoms($dataSymptoms, $dataCharacter);
        
        
        // $outputData = [
        //     'inputData'              => $dataSymptoms,
        //     'dataCharacter'              => $dataCharacter,
        // ];
        // return $this->respond(sizeOf($dataCharacter), 200);

        if($symptoms == true){
            $output = [
                'status' => 200,
                'message' => 'Berhasil Tambah Gejala'
            ];
            return $this->respond($output, 200);
        } else {
            $output = [
                'status' => 400,
                'message' => 'Gagal Tambah Gejala'
            ];
            return $this->respond($output, 400);
        }
    }

    public function edit_symptoms()
    {
        $id = $this->request->getVar('id');
        $code = $this->request->getVar('code');
        $name  = $this->request->getVar('name');
        $suggest = $this->request->getVar('suggestion');
        $userId = 'qwerty123';
        $updated_at			= date("Y-m-d H:i:s");
        $updated_by  		= $userId;

        $dataSymptom = [
            'code'              => $code,
            'name'              => $name,
            'suggestion'        => $suggest,
            'updated_at'        => $updated_at,
            'updated_by'        => $updated_by
        ];

        $idData = [
            'id'                => $id
        ];

        $dataCharacterFromFe = $this->request->getVar('character');

        $idDataCharacter = [];
        $dataCharacter = [];

        foreach($dataCharacterFromFe as $dataValue) {
            $dataId = [
                'id'                => $dataValue->id
            ];

            $data = [
                'symptom_id'        => $id,
                'name'              => $dataValue->karakter,
                'suggestion'        => $dataValue->saranKarakter,
                'updated_at'        => date("Y-m-d H:i:s"),
                'updated_by'        => $userId,
                'deleted'           => '0',
            ];
            array_push($idDataCharacter, $dataId);
            array_push($dataCharacter, $data);
        }
        
        $symptoms = $this->symptom->update_symptoms($idData, $dataSymptom, $idDataCharacter, $dataCharacter);
        // return $this->respond($symptoms, 200);


        if($symptoms == true){
            $output = [
                'status' => 200,
                'message' => 'Berhasil Update Gejala'
            ];
            return $this->respond($output, 200);
        } else {
            $output = [
                'status' => 400,
                'message' => 'Gagal Update Gejala'
            ];
            return $this->respond($output, 400);
        }
    }

    public function delete_symptoms()
    {
        $id                 = $this->request->getVar('id');
        $updated_at			= date("Y-m-d H:i:s");
        $updated_by  		= $id;
        $deleted  		    = '1';

        $dataSymptom = [
            'updated_at'        => $updated_at,
            'updated_by'        => $updated_by,
            'deleted'           => $deleted,
        ];

        $idData = [
            'id'                => $id
        ];

        $idDataForeigKey = [
            'symptom_id'        => $id
        ];

        $symptoms = $this->symptom->delete_symptoms($idData, $dataSymptom, $idDataForeigKey);

        if($symptoms == true){
            $output = [
                'status' => 200,
                'message' => 'Gejala Berhasil di Hapus'
            ];
            return $this->respond($output, 200);
        } else {
            $output = [
                'status' => 400,
                'message' => 'Gejala Gagal di Hapus'
            ];
            return $this->respond($output, 400);
        }
    }


    // =============================================================


    public function addData()
    {

        $symptoms_id            = $this->getID();
        $code                   = $this->request->getVar('code');
        $name                   = $this->request->getVar('name');
        $user_id                = $this->request->getVar('user_id');

        $suggest                = $this->request->getVar('suggestion');
        $character                = $this->request->getVar('character');
        // $symptom_ids            = $this->request->getVar('id');
        // $symptom_detail_ids     = $this->request->getVar('symptom_detail_id');

        if (count($suggest) == 1) {
            $dataSymptom = [
                'id'                => $symptoms_id,
                'code'              => $code,
                'name'              => $name,
                'suggestion'        => $suggest[0],
                'created_at'        => NOWDATETIME,
                'created_by'        => $user_id,
                'deleted'           => '0',
            ];

            $createSymptom = $this->symptom->create_symptomv2($dataSymptom);
            return $this->respond($createSymptom, 200);
            if ($createSymptom != true) {
                $output = [
                    'status' => 400,
                    'message' => 'Gagal input data Gejala',
                    'error' => [
                        'createSymptom' => $createSymptom,
                        'createDetailSymptom' => false,
                    ]
                ];
                return $this->respond($output, 400);
            }

            $output = [
                'status' => 200,
                'message' => 'Berhasil input data Gejala'
            ];
            return $this->respond($output, 200);

        } else {
            $dataSymptom = [
                'id'                => $symptoms_id,
                'code'              => $code,
                'name'              => $name,
                'suggestion'        => "",
                'created_at'        => NOWDATETIME,
                'created_by'        => $user_id,
                'deleted'           => '0',
            ];

            $createSymptom = $this->symptom->create_symptomv2($dataSymptom);
            if ($createSymptom != true) {
                $output = [
                    'status' => 400,
                    'message' => 'Gagal input data Gejala',
                    'error' => [
                        'createSymptom' => $createSymptom,
                        'createDetailSymptom' => false,
                    ]
                ];
                return $this->respond($output, 400);
            }

            $dataDetailSymptom = [];
            foreach ($suggest as $i=>$val){
                $dataDetail = [
                    'id'                    => $this->getID(),
                    'symptom_id'            => $symptoms_id,
                    'name'                  => $character[$i],
                    'suggestion'            => $suggest[$i],
                    'created_at'            => NOWDATETIME,
                    'created_by'            => $user_id,
                    'deleted'               => '0',
                ];
                array_push($dataDetailSymptom, $dataDetail);
            }
            $createSymptomDetail = $this->symptom->create_symptom_detailv2($dataDetailSymptom);
            // return $this->respond($createSymptomDetail, 200);

            if ($createSymptomDetail != true) {
                $output = [
                    'status' => 400,
                    'message' => 'Gagal input data Gejala karakter',
                    'error' => [
                        'createSymptom' => $createSymptomDetail,
                    ]
                ];
                return $this->respond($output, 400);
            }

            $output = [
                'status' => 200,
                'message' => 'Berhasil input data Gejala'
            ];
            return $this->respond($output, 200);
            
        }
        
    }

    public function deleteData()
    {
        $symptom_id            = $this->request->getVar('id');

        // get validasi data
        $getSymptomId = $this->symptom->get_symptom_byid($symptom_id);
        if (empty($getSymptomId)) {
            $output = [
                'status' => 404,
                'message' => 'Gagal input data penyakit',
                'error' => [
                    'diseases_id' => 'ID tidak ditemukan'
                ]
            ];
            return $this->respond($output, 404);
        }
        
        $deleteSymptom         = $this->symptom->softclear_symptom($symptom_id);
        // return $this->respond($deleteSymptom, 200);
        $deleteSymptomDetail   = $this->symptom->softclear_symptom_detail($symptom_id);

        $output = [
            'status' => 200,
            'message' => 'Data Gejala telah dihapus',
            'data' => [
                'deleteSymptom' => $deleteSymptom,
                'deleteSymptomDetail' => $deleteSymptomDetail
            ]
        ];
        return $this->respond($output, 200);
    }

    public function detailData()
    {
        // return $this->respond('hap', 200);
        $symptom_id            = $this->request->getVar('id');

        // get validasi data
        $getSymptomId = $this->symptom->get_symptom_byid($symptom_id);
        if (empty($getSymptomId)) {
            $output = [
                'status' => 404,
                'message' => 'Gagal mencari data Obat',
                'error' => [
                    'symptom_id' => 'ID tidak ditemukan'
                ]
            ];
            return $this->respond($output, 404);
        }

        $getSymptomDetailId = $this->symptom->get_symptom_detail_byid($symptom_id);

        $output = [
            'status' => 200,
            'message' => 'Data obat ditemukan',
            'data' => [
                'symptom' => $getSymptomId,
                'symptom_detail' => $getSymptomDetailId,
            ]
        ];
        return $this->respond($output, 200);
    }

    public function editData()
    {   
        
        $symptoms_id            = $this->request->getVar('id');
        $code                   = $this->request->getVar('code');
        $name                   = $this->request->getVar('name');
        $user_id                = $this->request->getVar('user_id');

        $id_char                = $this->request->getVar('id_char');
        $suggest                = $this->request->getVar('suggestion');
        $character                = $this->request->getVar('character');

        // return $this->respond($symptoms_id, 200);

        // get validasi data
        $getSymptomId = $this->symptom->get_symptom_byid($symptoms_id);
        if (empty($getSymptomId)) {
            $output = [
                'status' => 404,
                'message' => 'Gagal mencari data Gejala',
                'error' => [
                    'symptom_id' => 'ID tidak ditemukan'
                ]
            ];
            return $this->respond($output, 404);
        }
        // return $this->respond($getSymptomId, 200);
        // return $this->respond(count($suggest), 200);

        if (count($suggest) == 1) {
            $dataSymptom = [
                'code'              => $code,
                'name'              => $name,
                'suggestion'        => $suggest[0],
                'updated_at'        => NOWDATETIME,
                'updated_by'        => $user_id,
                'deleted'           => '0',
            ];

            $whereSymptom = [
                'id'                => $symptoms_id,
            ];

            $updateSymptom = $this->symptom->update_symptomv2($dataSymptom, $whereSymptom);
            return $this->respond($updateSymptom, 200);
            if ($updateSymptom != true) {
                $output = [
                    'status' => 400,
                    'message' => 'Gagal update data Gejala',
                    'error' => [
                        'updateSymptom' => $updateSymptom,
                        'createDetailSymptom' => false,
                    ]
                ];
                return $this->respond($output, 400);
            }

            $output = [
                'status' => 200,
                'message' => 'Berhasil input data Gejala'
            ];
            return $this->respond($output, 200);

        } else {
            $dataSymptom = [
                'code'              => $code,
                'name'              => $name,
                'suggestion'        => "",
                'updated_at'        => NOWDATETIME,
                'updated_by'        => $user_id,
                'deleted'           => '0',
            ];

            $whereSymptom = [
                'id'                => $symptoms_id,
            ];

            $editSymptom = $this->symptom->update_symptomv2($dataSymptom, $whereSymptom);
            if ($editSymptom != true) {
                $output = [
                    'status' => 400,
                    'message' => 'Gagal input data Gejala',
                    'error' => [
                        'createSymptom' => $editSymptom,
                        'createDetailSymptom' => false,
                    ]
                ];
                return $this->respond($output, 400);
            }
            // return $this->respond($editSymptom, 200);

            $dataDetailSymptomId = [];
            $dataDetailSymptom = [];

            foreach ($suggest as $i=>$val){
                $dataIdDetail = [
                    'id'                    => $id_char[$i],
                ];

                $dataDetail = [
                    'symptom_id'            => $symptoms_id,
                    'name'                  => $character[$i],
                    'suggestion'            => $suggest[$i],
                    'updated_at'            => NOWDATETIME,
                    'updated_by'            => $user_id,
                    'deleted'               => '0',
                ];

                array_push($dataDetailSymptomId, $dataIdDetail);
                array_push($dataDetailSymptom, $dataDetail);
            }

            // $output = [
            //     'status' => 200,
            //     'error' => [
            //         'dataDetailSymptomId' => $dataDetailSymptomId,
            //         'dataDetailSymptom' => $dataDetailSymptom,
            //     ]
            // ];
            // return $this->respond($output, 200);

            $updateSymptomDetail = $this->symptom->update_symptom_detailv2($dataDetailSymptom, $dataDetailSymptomId);

            // return $this->respond($updateSymptomDetail, 200);

            if ($updateSymptomDetail != true) {
                $output = [
                    'status' => 400,
                    'message' => 'Gagal update data Gejala karakter',
                    'error' => [
                        'updateSymptom' => $updateSymptomDetail,
                    ]
                ];
                return $this->respond($output, 400);
            }

            $output = [
                'status' => 200,
                'message' => 'Berhasil update data Gejala'
            ];
            return $this->respond($output, 200);

        }

        

    }
 
    // ===============================================================================

    public function detailSymptomsData()
    {
        $symptom_id            = $this->request->getGet('symptom_id');

        // get validasi data
        $getDetailSymptomsId = $this->symptom->get_symptom_detail_byid($symptom_id);

        $output = [
            'status' => 200,
            'message' => 'Data gejala ditemukan',
            'data' => $getDetailSymptomsId
        ];
        return $this->respond($output, 200);
    }
}