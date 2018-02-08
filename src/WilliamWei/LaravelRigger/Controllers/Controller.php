<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 08/02/2018
 * Time: 10:54
 */

namespace WilliamWei\LaravelRigger\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    protected $repository;

    protected $repositoryNameSpace;
    protected $entityNameSpace;

    public function __construct()
    {
        $this->entityNameSpace = 'App\\'.config('rigger.paths.models').'\\';
        $this->repositoryNameSpace = 'App\\'.config('rigger.paths.repositories').'\\';
    }


    public function index(Request $request) {
        $this->repository = $this->resolveRepository($request);
        return $this->repository->all();
    }

    public function show(Request $request, $id) {
        $this->repository = $this->resolveRepository($request);
        return $this->repository->find($id);
    }

    public function update(Request $request, $id) {
        $this->repository = $this->resolveRepository($request);
        $payload = $request->all();
        return $this->repository->update($payload, $id);
    }

    public function store(Request $request) {
        $this->repository = $this->resolveRepository($request);
        $payload = $request->all();
        return $this->repository->create($payload);
    }

    public function destroy(Request $request, $id) {
        $this->repository = $this->resolveRepository($request);
        $this->repository->delete($id);
    }

    protected function resolveRepository(Request $request) {
        return app($this->repositoryNameSpace.$request->attributes->get('rigger_entity').'Repository');
    }

    protected function resolveEntity(Request $request) {
        return app($this->entityNameSpace.$request->attributes->get('rigger_entity'));
    }

}