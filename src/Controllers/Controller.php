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
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;
use WilliamWei\LaravelRigger\Transformers\Transformer;


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
        return $this->response($request, $this->repository->all());
    }

    public function show(Request $request, $id) {
        $this->repository = $this->resolveRepository($request);
        return $this->response($request, $this->repository->find($id));
    }

    public function update(Request $request, $id) {
        $this->repository = $this->resolveRepository($request);
        $payload = $request->all();
        return $this->response($request, $this->repository->update($payload, $id));
    }

    public function store(Request $request) {
        $this->repository = $this->resolveRepository($request);
        $payload = $request->all();
        return $this->response($request, $this->repository->create($payload));
    }

    public function destroy(Request $request, $id) {
        $this->repository = $this->resolveRepository($request);
        return $this->response($request, $this->repository->delete($id));
    }

    protected function resolveRepository(Request $request) {
        return app($this->repositoryNameSpace.$request->attributes->get('rigger_entity').'Repository');
    }

    protected function resolveEntity(Request $request) {
        return app($this->entityNameSpace.$request->attributes->get('rigger_entity'));
    }

    protected function response($request, $data, TransformerAbstract $transformer = null) {
        $response = fractal();
        if (! $transformer) {
            $transformer = new Transformer();
        }
        if(is_array($data) || $data instanceof Collection) {
            $response->collection($data, $transformer);
        } else {
            $response->item($data, $transformer);
        }
        return $response->parseIncludes($request->input('include'))->toArray();
    }

}