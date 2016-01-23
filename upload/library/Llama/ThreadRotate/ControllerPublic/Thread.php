<?php

/**
 * Llama/ThreadRotate/ControllerPublic/THread
 *
 * @package    Llama: Thread Rotate
 * @author     Lazy Llama / Nigel Hardy
 * @copyright  2015 Nigel Hardy
 */

class Llama_ThreadRotate_ControllerPublic_Thread extends XFCP_Llama_ThreadRotate_ControllerPublic_Thread
{
	public function actionRotate()
	{
		
		$threadId = $this->_input->filterSingle('thread_id', XenForo_Input::UINT);
		$newTitle = $this->_input->filterSingle('newTitle', XenForo_Input::STRING);
		$ftpHelper = $this->getHelper('ForumThreadPost');
		list($thread, $forum) = $ftpHelper->assertThreadValidAndViewable($threadId);

		$threadModel = $this->_getThreadModel();
		
		if (!XenForo_Visitor::getInstance()->hasPermission('forum', 'manageAnyThread'))
		{
			throw $this->getErrorOrNoPermissionResponseException(false);
		}
		$viewingUser =  XenForo_Visitor::getInstance();
		
		if ($this->isConfirmedPost()) // rotate the thread
		{
		    
			/* Let's create the DataWriter associated on Threads */
			$threadWriter = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread', XenForo_DataWriter::ERROR_SILENT);
			$threadWriter->setExtraData(XenForo_DataWriter_Discussion_Thread::DATA_FORUM, $forum);
			$threadWriter->setOption(XenForo_DataWriter_Discussion::OPTION_TRIM_TITLE, true);
			$threadWriter->bulkSet(array(
					'title' => $newTitle,
					'user_id' => $viewingUser['user_id'],
					'username' => $viewingUser['username'],
					'sticky' =>  $thread['sticky'],
					'discussion_open' => $thread['discussion_open'],
					'discussion_state'=> $thread['discussion_state'],
					'prefix_id' => $thread['prefix_id'],
					'node_id' => $thread['node_id']
			));
			
			$originalUser = $this->getModelFromCache('XenForo_Model_User')->getUserById($thread['user_id']);
			
			$params['title'] = $thread['title'];
			$params['username'] = $thread['username'];
			$params['userLink'] = XenForo_Link::buildPublicLink('canonical:members', $originalUser);
			$params['link'] = XenForo_Link::buildPublicLink('full:threads', $thread);
			
			$message = new XenForo_Phrase('llama_rt_new_rotated_thread_message', $params, false);

			$postWriter = $threadWriter->getFirstMessageDw();
			$postWriter->set('message', $message->render());
			$postWriter->setExtraData(XenForo_DataWriter_DiscussionMessage_Post::DATA_FORUM, $forum);

			/* Save all changes and we are done with new thread! */
				
			$result = $threadWriter->save();
			$newThreadId = $threadWriter->get('thread_id');
			list($newThread, $newForum) = $ftpHelper->assertThreadValidAndViewable($newThreadId);

			/* Add new post at end of old thread */
			
			$params['title'] = $newTitle;
			$params['link'] = XenForo_Link::buildPublicLink('full:threads', $newThread);
			$message = new XenForo_Phrase('llama_rt_old_rotated_thread_message', $params, false);
				
			$postWriter = XenForo_DataWriter::create('XenForo_DataWriter_DiscussionMessage_Post');
			$postWriter->set('user_id', $viewingUser['user_id']);
			$postWriter->set('username', $viewingUser['username']);
			$postWriter->set('message', $message->render() );
			$postWriter->set('message_state', $this->_getPostModel()->getPostInsertMessageState($thread, $forum));
			$postWriter->set('thread_id', $threadId);
			$postWriter->setExtraData(XenForo_DataWriter_DiscussionMessage_Post::DATA_FORUM, $forum);
			$postWriter->save();
			
			/* Close and unstick original thread */
			
			$dwInput = array(
					'discussion_open' => FALSE,
					'sticky' => FALSE
			);
			

			$threadWriter = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread');
			$threadWriter->setExistingData($threadId);
			$threadWriter->bulkSet($dwInput);
			$threadWriter->setExtraData(XenForo_DataWriter_Discussion_Thread::DATA_FORUM, $forum);
			$threadWriter->save();
			
			
			XenForo_Model_Log::logModeratorAction(
			'thread', $thread, 'rotate', array('new_title' => $newTitle)
			);
		
			return $this->responseRedirect(
					XenForo_ControllerResponse_Redirect::SUCCESS,
					XenForo_Link::buildPublicLink('full:threads', $newThread)
			);
		}
		else // show a rotate confirmation dialog
		{	
			// Guess the new title
			
			$newTitleGuess =  preg_replace_callback( "#(\d+)$#", function ($m) {return ++$m[1]; } , $thread['title']);
			$viewParams = array(
					'newTitleGuess' => $newTitleGuess,
					'thread' => $thread
			);
			
			return $this->responseView('Llama_ViewPublic_Thread_Rotate', 'llama_thread_rotate', $viewParams);
			
		}
		
		
		
	}
}
