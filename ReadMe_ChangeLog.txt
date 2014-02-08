==============================================================================================
                                     PocketMine Essentials Package
                                                by Kevin Wang
----------------------------------------------------------------------------------------------
                                     Package Version: 4.1.8-Alpha
----------------------------------------------------------------------------------------------
Skype: kvwang98 ( The one without _rec after the username )
E-Mail: kevin@cnkvha.com
----------------------------------------------------------------------------------------------
                                    Join My MCPE Server: 
                                    mcpe.MineConquer.com
==============================================================================================

What's New: 
  - 4.1.8 Alpha ( 2014/2/8 )
      - Fixed the signs case bug
        (If you don't update, players can type [Free] instead of [free] to make free signs)
  - 4.1.7 Alpha ( 2014/2/8 )
      - Fixed the API bugs in the last update
  - 4.1.6 Alpha ( 2014/2/7 )
      - Added $api->pmess->SendBlockUpdateRAW(); method in PMEssAPI
        (There will be a HUGE update after this! )
  - 4.1.5 Alpha ( 2014/2/6 )
      - Added Sign things(Warp, World, Free and Spawn signs. )
        (With new PermissionNodes. )
      - Added WarpAPI($api->warp)
      - The [World] signs can automatic update with player numbers
  - 4.1.4 Alpha ( 2014/1/22 )
      - Fixed PMEssProtect module bug. (Issue 51 by @Hetal728)
  - 4.1.3 Alpha ( 2014/1/8 )
      - Supported API 12
  - 4.1.2 Alpha ( 2014/1/13 )
      - Fixed Redstone crash bug (Issue 46 by @Georggi)
  - 4.1.1 Alpha ( 2014/1/10 )
      - Added a new permission that make a player can not be banned(Issuer 42 by @Georggi)
        ( See ReadMe_PermissionNodes.txt )
      - Added a new event when player touchs protected signs
        (player.block.touch.protected)
      - Added "/wlist" to list all worlds (PermNode: &.wlist)
      - Fixed the PMEssWarp text(mis-typed word) bug
      - Fixed SuperSword error bug (Issue 38 by @Georggi)
      - Fixed ChestLock (Issue 35 by @Georggi)
  - 4.1.0 Alpha ( 2014/1/5 )
      - Added a new module: PMEssWarps ( /warp, /setwarp, /delwarp )
      - Fixed the SetWorldSpawn module
      - Fixed player can not un-vanish
  - 4.0.1 Alpha ( 2014/1/3 )
      - Fixed ChestProtect bug (Issue #28 by @Georggi)
      - Added Protection Bypass Mode ( /pbypass, PermNode: "&.pbypass" and "pmess.protect.bypass" )
  - 4.0.0 Alpha ( 2014/1/3 )
      - Fixed PMEssLoader not loading FlyMode Module
      - Fixed PMEssProtect crash bug after fixing (WTF, again! )
  - 4.0.0 Beta ( 2014/1/3 )
      - Added Fly Mode in Survival mode( /fly )
        (Look straight to fly straight, Look up to fly up, look down to fly down)
      - Fixed PMEssProtect error bug(Again O_o) (Issue 23 by @Georggi)
  - 3.6.9 Alpha ( 2014/1/2 )
      - Fixed PrimedTNT Disguise Permission Node (Issue 20 by @iksaku)
      - Fixed PMEssProtect same area protect in all worlds(Issue 21 by @Georggi)
  - 3.6.8 Alpha ( 2014/1/1 )
      - Really fixed PMEssProtect bug(Last fix faild)
      - Made PMEssProtect area detection a little bit faster
  - 3.6.7 Alpha ( 2013/12/30 )
      - Fixed the PMEssProtect bug
      - Fixed the display text problems in PMEssProtect
      - *HideNSeek Plugin still work with this update! 
         (http://github.com/kvwang98/HideNSeek)
  - 3.6.6 Alpha ( 2013/12/30 )
      - Fixed the PMEssAPI Error
      - Changed the time interval of updating the position when disguised as a block
      - Added a function to CoreAPI
        ( $api->pmess->undisguise(Player $player); )
  - 3.6.5 Alpha ( 2013/12/29 )
      - Added a function to CoreAPI
        ( $api->pmess->disguiseAsBlock(Player $player, int $blockID); )
      - Fixed player can do any commands when DISABLED GroupManager :O
      - Added some code obfuscation due to prevent people read and steal my code. :D
  - 3.6.4 Alpha ( 2013/12/28 )
      - Added Multi-Protection
         (/protect, /protect set [Protection ID] , /unprotect [Protection ID])
      - Fixed some GroupManager Group(Rank) time limit bugs
      - Now disk space will be free if you move someone to Default group forever
      - Added alias to /swspawn for the command /setwspawn
      - Fixed the iControlU help text
      - Fixed WorldSpawn module not loading
      - Added /spawn to default commands for GroupManager
  - 3.6.3 Beta ( 2013/12/22 )
      - Added World Spawn Changer ( /setwspawn ), suggested by iksasu on Github(issue 1)
      - Fixed the bugs(Github issue 3 and 5) reported by iksasu. 
      - Fixed God Mode doesn't work. (Github issue 6)
  - 3.6.2 Beta ( 2013/12/22 )
      - Fixed lots of DisguiseCraft bugs. 
        (You won't lose your disguise when vanish/unvanish)
        (You won't lose your disguise when teleport to another world)
      - Fixed some errors
        (Removed case-insensitivity in Session API)
  - 3.6.1 Beta ( 2013/12/21 )
      - Added BlockDisguise permissions for each block ID
        ("pmess.disguisecraft.block" for all blocks)
        ("pmess.disguisecraft.block.[ID]" for only a specfied block)
        (For example, "pmess.disguisecraft.block.20" is only for glass block)
      - Added(Fixed) Signs and Bucket protection for Area Protect Module
      - Fixed the Redstone Command Sign permission detection bug
  - 3.6.0 Beta ( 2013/12/19 )
      - Fixed the iControlU bug
      - Added Disguise as a Primed TNT
      - Added Disguise as a Moveable Block
      - Rewrote the DisguiseCraft code structure
  - 3.5.7 Alpha ( 2013/12/18 )
      - Fixed Portal to World errors and bugs. 
      - Fixed the Undefined variable "isVanished" error bug randomly. 
  - 3.5.6 Alpha ( 2013/12/18 )
      - Added Colorful Chat support. 
        ( Use $X, which X is color code, the same as PC color code. )
        ( Currently only work on console.  )
      - Fixed username bug when disguising as a player
      - Fixed loading external plugins depends on PMEss
  - 3.5.5 Alpha ( 2013/12/17 )
      - Supported PocketMine-MP Alpha_1.3.11dev ( MCPE 0.8.0 )
        - Added PowerTool ( /pt ), touch a block by holding a specified item to run a command. 
        - Fixed bugs when disabled GroupManager. 
  - 3.5.4 Alpha ( 2013/12/6 )
      - Fixed some errors when doing something(chat, command, break/place block, etc. ). 
  - 3.5.3 Beta ( 2013/11/25 )
      - Added a config for GroupManger to disable it. 
      - Fixed server crash when disguising as a mob. 
      - Fixed chat username doesn't change when disguising as a player. 
  - 3.5.2 Beta ( 2013/11/24 )
      - Added a loader to load all things in correct order
  - 3.5.1 Beta ( 2013/11/24 )
      - *(Security)* Fixed GroupManager Command Permission Check Bug
      - GroupManager no longer support OP-Override
           - That means OP system won't work, and you need to add
              commands to your group to make it usable for your group. 
           - Check this link for preset configs: 
              > http://forums.pocketmine.net/index.php?threads/826/
  - 3.5.0 Beta ( 2013/11/24 )
      - No longer require SRC modify! 
      - Added Session Systems (With an API)
         - Server creates a session with player's CID when he/she joins. 
         - Server destory a session when player disconnect. 
         - Support default value
  - 3.4.1 Beta ( 2013/11/2 )
      - Fixed the Rank Time Limit from GroupManager
      - Added a new event: player.afteerjoin
      - Added AutoInstaller for Windows OS
  - 3.4.0 Beta ( 2013/11/1 )
      - Added Time Limit for GroupManager
         You may do "/manuadd <GROUP> <USERNAME> [Days] [ExpireGroup]"
         (If you don't input [Days] and [ExpireGroup] it will be a life-time rank)
      - Added event: pmess.groupmanager.rankexpire
         (See Tutorial_Events.txt)
  - 3.3.3 Alpha (2013/10/30)
      - Made $API->console->run(); more secure 
  - 3.3.2 Alpha ( 2013/10/23 )
      - Fixed /unlock command in ChestLock
  - 3.3.1 Alpha ( 2013/10/20 )
      - Fixed the iControlU Console Spam/Crash bug
  - 3.3.0 Alpha ( 2013/10/20 )
      - Added NoFloatingTrees ( To fix the Tree Drop Bug )
      - Fixed PocketMine-MP Tree Drop Bug ( Now you can get all drops )
      - Fixed Unusual Username Crash server bug
  - 3.2.0 Alpha ( 2013/10/16 )
      - Added Mute Commands ( /mute and /unmute )
      - Released Alpha Version ( Stable Version )
  - 3.1.2 Beta ( 2013/10/15 )
      - Fixed iControlU Uncontrollable PermissionNode
      - Fixed iControlU Server lag and make it more exciting and amazing! 
      - Notice: OP have all PermissionNodes if OP-Override set to true! 
  - 3.1.1 Beta ( 2013/10/15 )
      - Fixed the login bug(/chat-on and /chat-off logic )
  - 3.1.0 Beta ( 2013/10/13 )
      - Added iControlU! 
      - Added 2 Built-In PermissionNodes( See ReadMe_PermissionNodes.txt )
  - 3.0.0 Beta ( 2013/10/12 )
      - Fixed GroupManager OP-Override, set to true to allow OP use any commands in the config
      - Added PermissionNodes into GroupManager
      - Changed the permission format for commands to "&.COMMAND", such as "&.kill"
      - Added some built-in permission nodes for some plugins in the package( See ReadMe_PermissionNodes.txt )
      - Old Version Configs are NOT compatible anymore 
        To fix configs, see "WARNING-Old_Version_Config_Incompatible.txt"
  - 2.3.0 Beta ( 2013/10/11 )
      - Added All Chat Disable ( /chat-on, /chat-off )
      - Fixed the compatible problems between Chest Lock and Protect plugin
      - Improved chat expirence ( Auto New-Line )
  - 2.2.0 Beta ( 2013/10/10 )
      - Added Chest Lock ( Stand on a chest and type /lock or /unlock )
  - 2.1.4 Alpha ( 2013/10/9 )
      - Fixed user can not completely leave a group(GroupManager)
      - Improved the security of Redstone Command Signs
        (It will check permission before running the command)
  - 2.1.3 Alpha ( 2013/10/8 )
      - Some redstone updates, now it is running faster
  - 2.1.2 Alpha ( 2013/10/6 )
      - Fixed InfWorldAPI crash bug 
  - 2.1.1 Alpha ( 2013/10/5 )
      - Pistons now will cause block updates(Sand and gravel will fall)
  - 2.1.0 Beta ( 2013/10/2 )
      - Added ExplosiveBlock to Redstone plugin
  - 2.0.0 Beta ( 2013/10/1 )
      - Added GroupManager system ( Support OP-OverRide Config ) 
      - Added RedStone system
      - Fixed some version bugs in plugin files
  - 1.1.0 Alpha ( 2013/9/19 )
      - Added Home Set commands. (/sethome, /home) 
      - Added Teleport Request commands. (/tpa, /tpaccept, /tpdeny) 
  - 1.0.0 Alpha ( 2013/9/18 )
      - Fixed a lot of known bugs. 
  - 1.0.0 Beta ( 2013/9/14 )
      - First Release
