<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin\system\attachment;


use crmeb\basic\BaseController;
use app\common\repositories\system\attachment\AttachmentCategoryRepository;
use app\common\repositories\system\attachment\AttachmentRepository;
use crmeb\services\UploadService;
use Exception;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

/**
 * Class Attachment
 * @package app\controller\admin\system\attachment
 * @author xaboy
 * @day 2020-04-16
 */
class Attachment extends BaseController
{
    /**
     * @var AttachmentRepository
     */
    protected $repository;

    /**
     * @var int
     */
    protected $merId;

    /**
     * Attachment constructor.
     * @param App $app
     * @param AttachmentRepository $repository
     */
    public function __construct(App $app, AttachmentRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
        $this->merId = $this->request->merId();
    }

    /**
     * @param int $id
     * @param string $field
     * @param AttachmentCategoryRepository $repository
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-15
     */
    public function image($id,$field, AttachmentCategoryRepository $repository)
    {
        $file = $this->request->file($field);
        if (!$file)
            return app('json')->fail('请上传图片');
        $file = is_array($file) ? $file[0] : $file;
        if ($id) {
            if (!$category = $repository->get($id, $this->merId))
                return app('json')->fail('目录不存在');
            $info = [
                'enname' => $category->attachment_category_enname,
                'id' => $category->attachment_category_id
            ];
        } else {
            $info = [
                'enname' => 'def',
                'id' => 0
            ];
        }

        validate(["$field|图片" => [
            'fileSize' => 2097152,
            'fileExt' => 'jpg,jpeg,png,bmp,gif',
            'fileMime' => 'image/jpeg,image/png,image/gif',
        ]])->check([$field => $file]);

        $upload = UploadService::create();
        $data = $upload->to($info['enname'])->move($field);
        if ($data === false) {
            return app('json')->fail($upload->getError());
        }
        $res = $upload->getUploadInfo();
        $res['dir'] = path_to_url($res['dir']);
        if (strpos($res['dir'], 'http') === false) $res['dir'] = $this->request->domain() . $res['dir'];
        $data = [
            'attachment_category_id' => $info['id'],
            'attachment_name' => $file->getOriginalName(),
            'attachment_src' => $res['dir']
        ];
        $this->repository->create(1, $this->merId, $this->request->adminId(), $data);
        return app('json')->success(['src' => $data['attachment_src']]);
    }

    /**
     * 获取列表
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author 张先生
     * @date 2020-03-27
     */
    public function getList()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params([['attachment_category_id', 0]]);
        $where['user_type'] = $this->merId;
        return app('json')->success($this->repository->getList($where, $page, $limit));
    }

    /**
     * @param AttachmentCategoryRepository $attachmentCategoryRepository
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020-04-16
     */
    public function batchChangeCategory(AttachmentCategoryRepository $attachmentCategoryRepository)
    {
        [$ids, $attachment_category_id] = $this->request->params([['ids', []], 'attachment_category_id'], true);
        if ($attachment_category_id && !$attachmentCategoryRepository->merExists($this->merId, $attachment_category_id))
            return app('json')->fail('分类不存在');
        if (!is_array($ids) || !count($ids))
            return app('json')->fail('请选择要修改分类的附件');
        $this->repository->batchChangeCategory(array_map('intval', $ids), intval($attachment_category_id), $this->merId);
        return app('json')->success('图片移动成功');
    }

    /**
     * 批量删除
     *
     * @return Json
     * @throws Exception
     * @author 张先生
     * @date 2020-03-30
     */
    public function delete()
    {
        $ids = (array)$this->request->param('ids', []);
        if (!count($ids))
            return app('json')->fail('数据不存在');
        $this->repository->batchDelete($ids, $this->merId);
        return app('json')->success('删除成功');
    }

}
