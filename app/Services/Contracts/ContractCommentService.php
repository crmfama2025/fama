<?php

namespace App\Services\Contracts;

use App\Repositories\Contracts\CommentRepository;
use App\Repositories\Contracts\ContractRepository;
use App\Services\BrevoService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContractCommentService
{
    public function __construct(
        protected CommentRepository $commentRepo,
        protected BrevoService $brevoService,
        protected ContractRepository $contractRepository
    ) {}

    public function getAll()
    {
        return $this->commentRepo->all();
    }

    public function getById($id)
    {
        return $this->commentRepo->find($id);
    }

    public function getByContractId($contract_id)
    {
        return $this->commentRepo->getByContractId($contract_id);
    }

    public function create(array $data)
    {
        if ($data['contract_status'] != 4) {
            $this->validate($data);
        }

        if ($data['contract_status']) {
            // dump($data);
            contractStatusUpdate($data['contract_status'], $data['contract_id']);
            $contract = $this->contractRepository->find($data['contract_id']);

            $approvalUrl = route('contract.approve', [
                'id' => $contract->id
            ]);
            if ($contract->contract_status == 4) {
                $result = $this->brevoService->sendEmail(
                    [
                        ['email' => 'rahmathrasmiya@gmail.com', 'name' => 'Test User']
                    ],
                    'Kindly Review and Approve Contract',
                    'admin.emails.contract-approval-email',
                    [
                        // 'name'           => 'Test User',
                        'contractNumber' => $contract->project_number,
                        'approvalUrl'    => $approvalUrl
                    ]
                );
            }
        }

        return $this->commentRepo->create($data);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
