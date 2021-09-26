<?php
$fk_add_meta_nonce = wp_create_nonce( 'nds_add_user_meta_form_nonce' );
$fk_parameters = json_decode(do_shortcode('[fk_admin_parameters]'));
if (!empty($_POST['submitButton'])){
    echo 'recibido un submit de la pagina de configuración';
    die();
}
?>
<div class="wrap">
    <div class="bootstrap">
        <div class="alert alert-danger error-config-frakmenta" style="display:none">
            Debes indicar un modo de conexión para frakmenta
        </div>
    </div>
    <div class="bootstrap">
        <div id="frakmenta-wrapper">
            <div id="general" frakmenta-tab-content>
                <div class="box half left">
                   <div class="logo-fk"></div>
                    <p>Frakmenta es una solución de financiación adaptada a las necesidades de los clientes</p>
                </div>

                <div class="box half right">
                    <ul class="tick">
                        <li><span class="frakmenta-bold">Facilita el pago a tus clientes</span></li>
                        <li><span class="frakmenta-bold">Ofrece una solución adaptada a sus necesidades de pago</li>
                        <li><span class="frakmenta-bold">Incrementa tus ventas, tu ticket medio y mejora la experiencia de compra de tus clientes</li>
                        <li><span class="frakmenta-bold">Que la forma de pago sea un beneficio para tu negocio</span></li>
                    </ul>
                </div>

                <div class="frakmenta-clear"></div>


                <!--{if $language_store}-->
                <div class="frakmenta-clear"></div><hr>
                <input type="hidden" id="fk_exists" value="<?php echo $fk_parameters->FRAKMENTA_EXIST_ACCOUNT;?>">



                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="frakmenta_configuration">


                    <input type="hidden" name="action" value="fk_form_response">
                    <input type="hidden" name="fk_add_user_meta_nonce" value="<?php echo $fk_add_meta_nonce ?>" />
                    <input type="hidden" name="fk_mode" id="fk_mode" value="<?php echo $fk_parameters->FRAKMENTA_MODE;?>"/>

                    <!--Frakmenta configuration-->
                    <div class="box">
                        <h3 class="inline">Configurar frakmenta es muy sencillo</h3>
                        <div style="line-height: 20px; margin-top: 8px">
                            <label>¿Tienes una cuenta de frakmenta?</label>&nbsp;&nbsp;
                            <input type="radio" name="fk_account" id="frakmenta_business_account_no" onclick="frakmentaAccount(0)" value="0" <?php if ($fk_parameters->FRAKMENTA_EXIST_ACCOUNT==0) echo 'checked="checked"';?> /> <label for="frakmenta_business_account_no">No</label>
                            <input type="radio" name="fk_account" id="frakmenta_business_account_yes" onclick="frakmentaAccount(1)" value="1" <?php if ($fk_parameters->FRAKMENTA_EXIST_ACCOUNT==1) echo 'checked="checked"';?> style="margin-left: 14px" /> <label for="frakmenta_business_account_yes">Si</label>
                        </div>
                    </div>

                    <div class="frakmenta-clear"></div><hr />
                    <div id="fk_register">
                        <br/><br/>
                        <div data-open-account-section id="account">
                            <h3 class="inline">Como empezar a trabajar con frakmenta</h3>
                            <br/><br/>
                            <ul>
                                <li>Para que funcione el plugin, tendrás que crear tu cuenta con frakmenta. Puedes hacerlo <a target="_blank" href="https://static.frakmenta.com/oferta-comercial">aquí</a></li>
                                <li>Desde frakmenta nos pondremos en contacto contigo para homologar tu comercio. También puedes llamarnos al 91 258 29 29 o escribirnos en infoclientes@frakmenta.com</li>
                                <li>Puedes activar el modo prueba para verificar el funcionamiento del módulo</li>
                                <li>Activa el módulo en modo producción ten en encuenta que vas a necesitar:</li>
                                <li>Introducir el código e-commerce que te ha asignado frakmenta</li>
                                <li>Introducir la Clave pública de acceso a frakmenta</li>
                                <li>Introducir la Clave privada de acceso a frakmenta</li>
                            </ul>
                            <br/>

                            <p>¿Necesitas ayuda?</p>
                            <p>No dudes en contactar a nuestro servicio de atención al cliente:</p>
                            <p>91 258 29 29</p>
                            <p>infoclientes@frakmenta.com</p>
                            <p>frakmenta.com/contacto-web</p>

                        </div>
                    </div>

                    <div data-configuration-section class="box " id="credentials">

                        <div class="right half" id="frakmenta-call-button">
                            <div id="frakmenta-call" class="box right"><span style="font-weight: bold">¿Necesitas ayuda?</span> <a target="_blank" href="https://frakmenta.com/contacto-web">Contáctanos</a></div>
                        </div>

                        <h3 class="inline">Configura tu conexión con frakmenta</h3>
                        <br /><br />

                        <div class="frakmenta-hide" id="configuration">
                            <h4>Indica a continuación tu información de conexión con frakmenta a Prestashop</h4>

                            <div id="fk-mode">
                                <p><span class="frakmenta-bold">Modo de conexión hacia frakmenta<br/><br/></span></p>
                                <button type="button" class="btn-mode mini ui button test_fk" onclick="frakmenta_mode(0)">Pruebas</button>
                                <button type="button" class="btn-mode mini ui button prod_fk" onclick="frakmenta_mode(1)">Producción</button>
                            </div>
                            <div id="standard-credentials" style="display: block" >
                            <dl>
                                <dt><label for="api_signature">Código e-commerce</label></dt>
                                <dd><input type='number' size="10" name="fk_merchant_id" id="fk_merchant_id" autocomplete="off" required /></dd>
                                <dt><label for="api_username">Llave pública</label></dt>
                                <dd><input type='text' name="fk_public_key" id="fk_public_key" autocomplete="off" size="85" required/></dd>
                                <dt><label for="api_password">Llave privada</label></dt>
                                <dd><input type='text' size="85" name="fk_private_key" id="fk_private_key" autocomplete="off" required/></dd>
                            </dl>
                            <div class="frakmenta-clear"></div>
                            <span class="description">Por favor verifica que la información proporcionada esté completa</span>
                        </div>
                        <div class="clear"></div>

                    </div>
                    <br /><br />
                    <h3 class="inline">Opciones adicionales de frakmenta</h3><br/>
                    <div class="row">
                        <div class="column-options-fk">
                            <strong>¿Deseas activar el simulador de frakmenta en los productos?</strong><br>
                            <select name="fk_sim_product" style="width:100px">
                                <option value="1" <?php if ($fk_parameters->FRAKMENTA_PRODUCT_OPTION==1) echo "selected";?> >Si</option>
                                <option value="0" <?php if ($fk_parameters->FRAKMENTA_PRODUCT_OPTION==0) echo "selected";?>>No</option>
                            </select>
                        </div>
                        <div class="column-options-fk">
                            <strong>¿Donde quieres colocar el simulador de frakmenta?</strong><br>
                            <select name="fk_location_simulator">
                                <option value=".product-add-to-cart" >En la parte inferior del importe del producto</option>
                                <option value=".social-sharing" >En la parte inferior de las redes sociales</option>
                            </select>
                        </div>
                    </div>
                    <br /><br /><br />
                    <br /><br />
                    <input class="ui button teal text-uppercase btn-frakmenta" type="submit" name="submitButton" value="Guardar configuración" onclick="return validate_config_fk()" />
            </div>
            <div id="fk-test-conn" style="display:none">
                <hr id="line-test"/>

                <div class="frakmenta-hide box-fk" data-tls-check-section id="test-fk">
                    <h3 class="inline">Prueba tu conexión a frakmenta</h3>
                    <br /><br />
                    <span class="ui button teal sm btn-frakmenta" data-url="<?php echo $fk_parameters->FRAKMENTA_URL;?>" data-token="<?php echo $fk_parameters->FRAKMENTA_PUBLIC_KEY;?>" style="cursor: pointer;display: inline-block;" id="test_fk_connection"><b>Probar conexión a frakmenta</b></span>
                    <div style="margin-top: 10px;" id="test_fk_conection_result"></div>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>
</div>