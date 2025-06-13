<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

abstract class BaseService
{
    protected $repository;

    public function __construct()
    {
        
        $this->repository = $this->getRepository();
    }
    abstract public function getRepository();
    public function create($data)
    {
        DB::beginTransaction();
        try
        {
            $data=$this->repository->create($data);
            DB::commit();
            return [
                "data"=>$data,
                "success"=> true,
                "message"=>"Successfully created"
            ];
        }
        catch(Throwable $e)
        {
            Log::error($e->getMessage());
            DB::rollBack();
            return [
                "success"=> false,
                "message"=>"Failed to create"
            ];
        }
    }
    public function update($id, $data)
    {
        DB::beginTransaction();
        try
        {
            $data=$this->repository->update($id,$data);
            DB::commit();
            return [
                "success"=> true,
                "message"=>"Successfully updated",
                'data'=>$data
            ];
        }
        catch(Throwable $e)
        {
            Log::error($e->getMessage());
            DB::rollBack();
            return [
                "success"=> false,
                "message"=>"Failed to update"
            ];
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->repository->destroy($id);
            DB::commit();
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            DB::rollBack();
        }
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }
    public function getAll()
    {
        return $this->repository->getAll();
    }
    public function paging(array $input){
        $pageSize = $input["page_size"] ?? 10;
        $conditions = $input["conditions"] ?? null;
        return $this->repository->paging( $pageSize, $conditions);
    }
    public function createOrUpdate($data)
    {
        DB::beginTransaction();
        try
        {
            if(!empty($data['id']))
            {
                $this->repository->update($data['id'],$data);
                DB::commit();
            return [
                "success"=> true,
                "message"=>"Successfully updated"
            ];
            }
            $this->repository->create($data);
            DB::commit();
            return [
                "success"=> true,
                "message"=>"Successfully created"
            ];
        }
        catch(Throwable $e)
        {
            Log::error($e->getMessage());
            DB::rollBack();
            return [
                "success"=> false,
                "message"=>"Failed to create"
            ];
        }
    }
}
