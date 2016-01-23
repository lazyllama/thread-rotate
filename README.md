[Llama] Rotate Thread add-on v1.2
Licensed under GNU GENERAL PUBLIC LICENSE Version 3
                       
[OUTLINE]
 - Locks an existing thread and starts a new thread in the same forum with just a few clicks. 
 - Adds posts in each thread linking to the other.
 - Copies stickiness and moderated status of old thread to new, and unsticks old thread.
 - If existing thread title ends in a number, increments that number in suggested new title, e.g. original thread "Pictures of Cats - Part 19", suggested new thread title "Pictures of Cats - Part 20".

[CHANGES]
1.2 - fixed issue if thread had a prefix
1.1a - fixed template/phrase error
1.1 - compatible with XF 1.4.x 
	- fixed "array_key_exists() expects parameter 2 to be array" error
	- fixed "deprecated preg_replace 'e' modfier" warning 
1.0 - initial release

[USE CASES]
Monthly threads - e.g. Close a "January Photos" thread and start a "February Photos" thread.
Preventing single threads becoming too large
(Large threads increase the PHP memory used and can prove difficult to move/delete in XenForo)
	
[INSTALLATION]
1. Copy all files/directories under `upload` to your XenForo's root
2. Import add-on xml

[DETAILED FUNCTIONALITY]
This adds a "Rotate Thread" option to the Thread Tools dropdown for any user with permissions to both edit and lock that thread.
The option can only be used if the user has "Manage (move, merge, etc.) thread by anyone" permissions for that forum.

When selected this performs the following actions:

1. Creates a new thread in the same forum (you can choose a new title)
2. Adds a post on the new thread with a link back to the old one.
3. Adds a post on the old thread with a link to the new one.
4. Locks the old thread.
5. If the old thread was "sticky" or moderated, the new one will be too.
6. If the old thread title ends in a number, the suggested new thread title will be incremented.

Editing the messages which get posted into the new and old thread.

	The messsages are two XenForo phrases:
	
		llama_rt_old_rotated_thread_message
		llama_rt_new_rotated_thread_message
	
	These can be edited like any other phrase.
	The "new" message uses the {link}, {title}, {userLink} and {username} variables.
	The "old" message uses the {link} and {title} variables.
