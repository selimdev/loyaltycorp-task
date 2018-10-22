<?php
declare(strict_types=1);

namespace Tests\App\Functional\Http\Controllers\MailChimp;

use Tests\App\TestCases\MailChimp\ListTestCase;
use Tests\App\TestCases\MailChimp\MemberTestCase;

class MembersControllerTest extends ListTestCase
{
    /**
     * Test application creates successfully list and returns it back with id from MailChimp.
     *
     * @return void
     */
    public function testAddMemberToListSuccessfully(): void
    {
        //create list
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        if (isset($list['mail_chimp_id'])) {
            $this->createdListIds[] = $list['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        }

        //create member
        $this->post(\sprintf('/mailchimp/lists/%s/members', $list['list_id']), static::$memberData);

        $content = \json_decode($this->response->getContent(), true);

        $this->assertResponseOk();
        $this->seeJson(static::$memberData);
        self::assertArrayHasKey('mail_chimp_unique_email_id', $content);
        self::assertNotNull($content['mail_chimp_unique_email_id']);
    }

    /**
     * Test application returns error response with errors when list validation fails.
     *
     * @return void
     */
    public function testCreateMemberValidationFailed(): void
    {
        //create list
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        if (isset($list['mail_chimp_id'])) {
            $this->createdListIds[] = $list['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        }

        //create member
        $this->post(\sprintf('/mailchimp/lists/%s/members', $list['list_id']));

        $content = \json_decode($this->response->getContent(), true);

        $this->assertResponseStatus(400);
        self::assertArrayHasKey('message', $content);
        self::assertArrayHasKey('errors', $content);
        self::assertEquals('Invalid data given', $content['message']);

        foreach (\array_keys(static::$memberData) as $key) {
            if (\in_array($key, static::$notRequired, true)) {
                continue;
            }

            self::assertArrayHasKey($key, $content['errors']);
        }
    }

    /**
     * Test application returns error response when member not found.
     *
     * @return void
     */
    public function testRemoveMemberNotFoundException(): void
    {
        //create list
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        if (isset($list['mail_chimp_id'])) {
            $this->createdListIds[] = $list['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        }

        //delete not existing member
        $memberId = 'invalid-member-id';
        $this->delete(\sprintf('/mailchimp/lists/%s/members/%s', $list['list_id'], $memberId));

        $this->assertMemberNotFoundResponse($memberId);
    }

    /**
     * Test application returns empty successful response when removing existing member.
     *
     * @return void
     */
    public function testRemoveMemberSuccessfully(): void
    {
        //create list
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        if (isset($list['mail_chimp_id'])) {
            $this->createdListIds[] = $list['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        }

        //create member
        $this->post(\sprintf('/mailchimp/lists/%s/members', $list['list_id']), static::$memberData);
        $member = \json_decode($this->response->content(), true);

        //delete member
        $this->delete(\sprintf('/mailchimp/lists/%s/members/%s', $list['list_id'], $member['member_id']));

        $this->assertResponseOk();
        self::assertEmpty(\json_decode($this->response->content(), true));
    }

    /**
     * Test application returns error response when list not found.
     *
     * @return void
     */
    public function testShowMemberNotFoundException(): void
    {
        //create list
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        if (isset($list['mail_chimp_id'])) {
            $this->createdListIds[] = $list['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        }

        //get not existing member
        $memberId = 'invalid-member-id';
        $this->get(\sprintf('/mailchimp/lists/%s/members/%s', $list['list_id'], $memberId));

        $this->assertMemberNotFoundResponse($memberId);
    }

    /**
     * Test application returns successful response with member data when requesting an existing member
     *
     * @return void
     */
    public function testShowMemberSuccessfully(): void
    {
        $list = $this->createList(static::$listData);

        $memberData = static::$memberData;
        $memberData['list_id'] = $list->getId();
        $member = $this->createMember($memberData);

        $this->get(\sprintf('/mailchimp/lists/%s/members/%s', $list->getId(), $member->getId()));
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (static::$memberData as $key => $value) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals($value, $content[$key]);
        }
    }


    /**
     * Test application returns error response when the member is not found
     *
     * @return void
     */
    public function testUpdateMemberNotFoundException(): void
    {
        //create list
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        if (isset($list['mail_chimp_id'])) {
            $this->createdListIds[] = $list['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        }

        //update not existing member
        $memberId = 'invalid-member-id';
        $this->put(\sprintf('/mailchimp/lists/%s/members/%s', $list['list_id'], $memberId));

        $this->assertMemberNotFoundResponse($memberId);
    }

    /**
     * Test application returns successfully response when updating existing list with updated values.
     *
     * @return void
     */
    public function testUpdateMemberSuccessfully(): void
    {
        //create list
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        if (isset($list['mail_chimp_id'])) {
            $this->createdListIds[] = $list['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        }

        //create member
        $this->post(\sprintf('/mailchimp/lists/%s/members', $list['list_id']), static::$memberData);
        $member = \json_decode($this->response->getContent(), true);

        //update member
        $this->put(
            \sprintf('/mailchimp/lists/%s/members/%s', $list['list_id'], $member['member_id']),
            ['status' => 'unsubscribed']
        );
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (\array_keys(static::$memberData) as $key) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals('unsubscribed', $content['status']);
        }
    }

    /**
     * Test application returns error response with errors when member validation fails
     *
     * @return void
     */
    public function testUpdateMemberValidationFailed(): void
    {
        //create list
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        if (isset($list['mail_chimp_id'])) {
            $this->createdListIds[] = $list['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        }

        //create member
        $memberData = static::$memberData;
        $memberData['list_id'] = $list['list_id'];
        $member = $this->createMember($memberData);

        $this->put(
            \sprintf('/mailchimp/lists/%s/members/%s', $list['list_id'], $member->getId()),
            ['status' => 'invalid']
        );
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseStatus(400);
        self::assertArrayHasKey('message', $content);
        self::assertArrayHasKey('errors', $content);
        self::assertArrayHasKey('status', $content['errors']);
        self::assertEquals('Invalid data given', $content['message']);
    }

}
