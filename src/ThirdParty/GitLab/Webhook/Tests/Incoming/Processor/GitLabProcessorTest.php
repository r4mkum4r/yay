<?php

namespace ThirdParty\GitLab\Webhook\Tests\Incoming\Processor;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use ThirdParty\GitLab\Webhook\Incoming\Processor\GitLabProcessor;

class GitLabProcessorTest extends TestCase
{
    public function providePayloads()
    {
        return [
            [
                'Push Hook',
                file_get_contents(__DIR__.'/Fixtures/PushWebhook.json'),
                'jsmith',
                'push',
            ],
            [
                'Merge Request Hook',
                file_get_contents(__DIR__.'/Fixtures/MergeRequestWebhook.json'),
                'jsmith',
                'merge_request.opened',
            ],
        ];
    }

    public function test_set_get_name(): void
    {
        $processor = new GitLabProcessor($name = 'gitlab-processor');
        $this->assertEquals('gitlab-processor', $processor->getName());
    }

    /** @dataProvider providePayloads */
    public function test_process_payload(
        string $header,
        string $contents,
        string $username,
        string $action
    ): void {
        $request = Request::create('/', 'POST', [], [], [], [], $contents);
        $request->headers->set('X-Gitlab-Event', $header);

        (new GitLabProcessor('gitlab-processor'))->process($request);
        $this->assertEquals($username, $request->request->get('username'));
        $this->assertEquals($action, $request->request->get('action'));
    }

    public function test_process_empty_payload_but_event(): void
    {
        $contents = json_encode([]);
        $request = Request::create('/', 'POST', [], [], [], [], $contents);

        (new GitLabProcessor('github-processor'))->process($request);
        $this->assertFalse($request->request->has('username'));
        $this->assertFalse($request->request->has('action'));
    }

    public function test_process_empty_payload(): void
    {
        $contents = json_encode([]);
        $request = Request::create('/', 'POST', [], [], [], [], $contents);
        $request->headers->set('X-Gitlab-Event', 'github-event');

        (new GitLabProcessor('github-processor'))->process($request);
        $this->assertFalse($request->request->has('username'));
        $this->assertFalse($request->request->has('action'));
    }

    /** @expectedException InvalidArgumentException */
    public function test_process_broken_payload(): void
    {
        $contents = ',1%}';
        $request = Request::create('/', 'POST', [], [], [], [], $contents);
        $request->headers->set('X-Gitlab-Event', 'github-event');

        (new GitLabProcessor('github-processor'))->process($request);
    }
}
