<?php

namespace Component\Engine;

final class Events
{
    const PRE_SAVE = 'yay.engine.pre_save';

    const POST_SAVE = 'yay.engine.post_save';

    const GRANT_PERSONAL_ACHIEVEMENT = 'yay.engine.grant_personal_achievement';

    const GRANT_PERSONAL_ACTION = 'yay.engine.grant_personal_action';

    const CREATE_PLAYER = 'yay.engine.create_player';

    const CHANGE_LEVEL = 'yay.engine.change_level';

    const CHANGE_SCORE = 'yay.engine.change_score';
}
