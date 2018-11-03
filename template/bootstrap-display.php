<?php

//Kint::dump($this->items);

?>

<div class="container">
    <div class="row">

    <?php foreach ($this->items as $user): ?>

        <div class="col-md-3">
            <div class="square-service-block">
                <span href="#">
                    <div class="ssb-icon"><i class="fa fa-paint-brush" aria-hidden="true"></i></div>
                    <h2 class="ssb-title"><?php
                        _e( empty($user->first_name) ? $user->user_login : $user->first_name." ".$user->last_name );

                        ?></h2>
                    <h5 style="color: #fff"><?php _e($user->user_email) ?></h5>
                                        <p style="color: #fff"><?php
                                            $user_roles = $this->get_role_list( $user );

                                            $roles_list = implode( ', ', $user_roles );

                                            _e($roles_list) ?></p>

                    <h5 style="color: #fff"><?php _e($user->phone) ?></h5>
<div>

    <?php

    $url = 'users.php?page=users-custom&';

    _e("<a class='submitdelete users_custom' href='" . wp_nonce_url( $url."action=remove&amp;user=$user->ID", 'bulk-users' ) . "'>" . __( 'Remove' ) . "</a>") ?>
</div>
                </span>
            </div>
        </div>

    <?php endforeach; ?>



    </div>
</div>