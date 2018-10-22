<?php
declare(strict_types=1);

namespace Tests\App\Unit\Http\Controllers\MailChimp;

use App\Http\Controllers\MailChimp\MembersController;
use Tests\App\TestCases\MailChimp\ListTestCase;

class MembersControllerTest extends ListTestCase
{
    /**
     * Test controller returns error response when exception is thrown during create MailChimp request.
     *
     * @return void
     */
    public function testCreateMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('post'));
        $list = $this->createList(static::$listData);

        $this->assertMailChimpExceptionResponse(
            $controller->create(
                $this->getRequest(static::$memberData),
                $list->getId()
            )
        );
    }

    /**
     * Test controller returns error response when exception is thrown during remove MailChimp request.
     *
     * @return void
     */
    public function testRemoveMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('delete'));
        $list = $this->createList(static::$listData);

        //create member
        $memberData = static::$memberData;
        $memberData['list_id'] = $list->getId();
        $member = $this->createMember($memberData);

        // If there is no list id, skip
        if (null === $list->getId()) {
            self::markTestSkipped('Unable to remove, no id provided for list');
            return;
        }

        $this->assertMailChimpExceptionResponse(
            $controller->remove(
                $list->getId(),
                $member->getId()
            )
        );
    }

    /**
     * Test controller returns error response when exception is thrown during update MailChimp request.
     *
     * @return void
     */
    public function testUpdateMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('patch'));
        $list = $this->createList(static::$listData);

        //create member
        $memberData = static::$memberData;
        $memberData['list_id'] = $list->getId();
        $member = $this->createMember($memberData);

        // If there is no list id, skip
        if (null === $list->getId()) {
            self::markTestSkipped('Unable to update, no id provided for list');
            return;
        }

        $this->assertMailChimpExceptionResponse(
            $controller->update(
                $this->getRequest(),
                $list->getId(),
                $member->getId()
            )
        );
    }
}
