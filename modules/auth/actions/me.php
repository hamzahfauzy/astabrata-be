<?php

use Libraries\Response;

return Response::json(__('profile retrieved'), request()->user());