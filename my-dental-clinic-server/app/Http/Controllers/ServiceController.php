<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ServiceService;
class ServiceController extends ApiResponseController
{
    protected $ServiceService ;
    public function __construct(ServiceService $ServiceService )
    {
        $this->ServiceService = $ServiceService;
    }
   public function createOrUpdate(Request $request)
   {
      $data=$request->all();

    $result = $this->ServiceService->createOrUpdate($data);
    if ($result['success']) {
        return $this->success($result['message']);
     } else {
     
         return $this->error($result['message']);
     }
   }
   public function paging(Request $request)
   {
    $input =$request->all();
    return $this->ServiceService->paging($input);
   }
   public function delete ($id)
   {
    $result=$this->ServiceService->destroy($id);
    return $result;
   }
   public function getByName (Request $request)
   {
    $name=$request->input('name');
    return $this->ServiceService->getByName($name);
   }
  
}
