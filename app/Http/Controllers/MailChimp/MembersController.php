<?php
declare(strict_types=1);

namespace App\Http\Controllers\MailChimp;

use App\Database\Entities\MailChimp\MailChimpMember;
use App\Database\Entities\MailChimp\MailChimpList;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mailchimp\Mailchimp;

class MembersController extends Controller
{
    /**
     * The MailChimp Api wrapper
     *
     * @var \Mailchimp\Mailchimp
     */
    private $mailChimp;

    /**
     * MembersController constructor
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Mailchimp\Mailchimp $mailchimp
     */
    public function __construct(EntityManagerInterface $entityManager, Mailchimp $mailchimp)
    {
        parent::__construct($entityManager);

        $this->mailChimp = $mailchimp;
    }

    /**
     * Create a MailChimp member
     *
     * @param \Illuminate\Http\Request $request
     * @param string $listId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, string $listId): JsonResponse
    {
        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $list = $this->entityManager->getRepository(MailChimpList::class)->find($listId);

        //check if the list exist
        if ($list === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }

        // Instantiate entity
        $member = new MailChimpMember($request->all());
        $member->setListId($list->getId());

        // Validate entity
        $validator = $this->getValidationFactory()->make($member->toMailChimpArray(), $member->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Save member into db
            $this->saveEntity($member);

            // add member into MailChimp list
            $response = $this->mailChimp->post(
                \sprintf('lists/%s/members', $list->getMailChimpId()),
                $member->toMailChimpArray()
            );
            // Set MailChimp id on the list and save list into db
            $this->saveEntity($member->setMailChimpUniqueEmailId($response->get('unique_email_id')));
        } catch (Exception $exception) {
            // Return error response if something goes wrong
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * Retrieve and return a  MailChimp member from a list
     *
     * @param string $listId
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $listId, string $memberId): JsonResponse
    {
        /** @var \App\Database\Entities\MailChimp\MailChimpMember|null $member */
        $member = $this->entityManager
            ->getRepository(MailChimpMember::class)
            ->findOneBy(['memberId' => $memberId, 'listId' => $listId]);

        //check if the member exist
        if ($member === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpMember[%s] not found', $memberId)],
                404
            );
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * Update a MailChimp member
     *
     * @param \Illuminate\Http\Request $request
     * @param string $listId
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $listId, string $memberId): JsonResponse
    {
        //get the list
        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $list = $this->entityManager->getRepository(MailChimpList::class)->find($listId);

        //check if the list exist
        if ($list === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }

        //get the member
        /** @var \App\Database\Entities\MailChimp\MailChimpMember|null $member */
        $member = $this->entityManager
            ->getRepository(MailChimpMember::class)
            ->findOneBy(['memberId' => $memberId, 'listId' => $listId]);

        //check if the member exist
        if ($member === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpMember[%s] not found', $memberId)],
                404
            );
        }

        // Update member properties
        $member->fill($request->all());

        // Validate entity
        $validator = $this->getValidationFactory()->make($member->toMailChimpArray(), $member->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Update the member into the database
            $this->saveEntity($member);

            // Update the member into MailChimp
            $this->mailChimp->patch(
                \sprintf('lists/%s/members/%s', $list->getMailChimpId(), $member->getSubscriberHash()),
                $member->toMailChimpArray()
            );
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * Delete MailChimp member from a list
     *
     * @param string $listId
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(string $listId, string $memberId): JsonResponse
    {
        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $list = $this->entityManager->getRepository(MailChimpList::class)->find($listId);

        //check if the list exist
        if ($list === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }

        /** @var \App\Database\Entities\MailChimp\MailChimpMember|null $member */
        $member = $this->entityManager
            ->getRepository(MailChimpMember::class)
            ->findOneBy(['memberId' => $memberId, 'listId' => $list->getId()]);

        //check if the member exist
        if ($member === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpMember[%s] not found', $memberId)],
                404
            );
        }

        try {
            // Remove list from database
            $this->removeEntity($list);
            // Remove list from MailChimp
            $this->mailChimp->delete(
                \sprintf('lists/%s/members/%s', $list->getMailChimpId(), $member->getSubscriberHash())
            );
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse([]);
    }

}
