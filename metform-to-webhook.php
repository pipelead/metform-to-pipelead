<?php
/*
Plugin Name: Metform to Pipelead
Plugin URI: https://pipelead.to
Description: Send Metform submissions to Pipelead
Version: 0.3
Author: Pipelead
*/


if (file_exists(plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php')) {
  require plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';

  $myUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
      'https://github.com/pipelead/metform-to-pipelead/',
      __FILE__,
      'metform-to-pipelead'
  );

  // Configurar para usar releases
  $myUpdateChecker->getVcsApi()->enableReleaseAssets();
  
  // Usar branch main
  $myUpdateChecker->setBranch('main');
  
  // Opcional: definir o tipo de estabilidade
  $myUpdateChecker->setStabilityFlags(array('stable' => true));
}


// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Iniciar sessão se ainda não estiver ativa
function mtp_start_session() {
  if (!session_id()) {
      session_start();
  }
  
  // Lógica do referrer
  if (!is_admin() && !isset($_POST['form_nonce'])) {
      // Só armazena se ainda não existir um referrer na sessão
      if (!isset($_SESSION['mtp_first_referrer'])) {
          $_SESSION['mtp_first_referrer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
      }
  }
}
add_action('init', 'mtp_start_session');

// Add menu item
add_action('admin_menu', 'mtp_add_admin_menu');
function mtp_add_admin_menu()
{
    add_menu_page(
        'Metform to Pipelead',
        'Metform Pipelead',
        'manage_options',
        'metform-pipelead',
        'mtp_settings_page',
        'dashicons-rest-api',
        100
    );
}

// Register settings
add_action('admin_init', 'mtp_register_settings');
function mtp_register_settings()
{
    register_setting('mtp_settings', 'mtp_webhooks');
}

// Add admin styles
add_action('admin_head', 'mtp_admin_styles');
function mtp_admin_styles()
{
    if (get_current_screen()->base != 'toplevel_page_metform-pipelead') {
        return;
    }
    ?>
    <style>
        .mtp-wrap {
            max-width: 1200px;
            margin: 20px;
        }
        .mtp-header {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .mtp-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .mtp-card {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .mtp-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .mtp-card input[type="url"] {
            width: 100%;
            margin-top: 10px;
        }
        .mtp-instructions {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #2271b1;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .mtp-instructions ol {
    margin: 15px 0 0 20px;
}

.mtp-instructions li {
    margin-bottom: 8px;
    line-height: 1.4;
}
    </style>
    <?php
}

add_action('admin_notices', 'mtp_admin_notices');
function mtp_admin_notices() {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Configurações salvas com sucesso.</p>
        </div>
        <?php
    }
}

// Admin page HTML
function mtp_settings_page()
{
    // Get all Metform forms
    $forms = get_posts([
        'post_type' => 'metform-form',
        'numberposts' => -1
    ]);

    // Get saved webhooks
    $webhooks = get_option('mtp_webhooks', array());
    ?>
    <div class="mtp-wrap">
        <div class="mtp-header">
            <h1>Integração Metform - Pipelead via Webhook</h1>
        </div>

        <div class="mtp-instructions">
    <h2>Como configurar</h2>
    <p>Para conectar seus formulários Metform ao Pipelead, siga estes passos:</p>
    <ol>
        <li>Acesse sua conta no Pipelead</li>
        <li>Navegue até a seção de Formulários</li>
        <li>Selecione ou crie um formulário</li>
        <li>Nas opções do formulário, clique em "endpoint"</li>
        <li>Copie o endpoint gerado</li>
        <li>Cole o endpoint no campo correspondente ao formulário desejado abaixo</li>
    </ol>
</div>

        <form method="post" action="options.php">
            <?php settings_fields('mtp_settings'); ?>
            
            <div class="mtp-grid">
                <?php foreach($forms as $form): ?>
                    <div class="mtp-card">
                        <h3><?php echo esc_html($form->post_title); ?></h3>
                        <div>
                            <label>URL de Endpoint do Pipelead:</label>
                            <input type="url" 
                                   name="mtp_webhooks[<?php echo $form->ID; ?>]" 
                                   value="<?php echo isset($webhooks[$form->ID]) ? esc_url($webhooks[$form->ID]) : ''; ?>" 
                                   placeholder="https://app.pipelead.to/webhook/...">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php submit_button('Salvar'); ?>
        </form>
    </div>
    <?php
}

// Check for Metform plugin dependency
function mtp_check_dependencies() {
  if (!is_plugin_active('metform/metform.php') && !is_plugin_active('metform-pro/metform.php')) {
      add_action('admin_notices', 'mtp_admin_notice_missing_metform');
      
      // Desativa o plugin
      deactivate_plugins(plugin_basename(__FILE__));
      
      // Se estiver tentando ativar o plugin, mostra erro
      if (isset($_GET['activate'])) {
          unset($_GET['activate']);
      }
  }
}
add_action('admin_init', 'mtp_check_dependencies');

// Admin notice HTML
function mtp_admin_notice_missing_metform() {
  $message = sprintf(
      esc_html__('O plugin %1$s requer o plugin Metform instalado e ativado. Por favor, %2$sinstale o Metform%3$s primeiro.', 'metform-to-pipelead'),
      '<strong>Metform to Pipelead</strong>',
      '<a href="' . esc_url(admin_url('plugin-install.php?s=metform&tab=search&type=term')) . '">',
      '</a>'
  );
  
  printf('<div class="error"><p>%1$s</p></div>', $message);
}

// Adicione isso para ter certeza que is_plugin_active está disponível
if (!function_exists('is_plugin_active')) {
  require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// Hook into Metform submission
add_action('metform_after_store_form_data', 'mtp_send_form_data_webhook', 10, 3);
function mtp_send_form_data_webhook($form_data, $form_settings, $form_id)
{
    $webhooks = get_option('mtp_webhooks', array());

    // Get form ID from form settings
    $form_post_id = isset($form_settings['id']) ? $form_settings['id'] : null;

    if (!$form_post_id || !isset($webhooks[$form_post_id]) || empty($webhooks[$form_post_id])) {
        return;
    }

    // Remove internal/system fields
    $excluded_fields = ['action', 'form_nonce', 'g-recaptcha-response-v3'];
    $submitted_data = array_diff_key($form_settings, array_flip($excluded_fields));

    // Remove id
    unset($submitted_data['id']);

    // Add current URI (it gets referer because it's a POST request)
    $submitted_data['current_uri'] = wp_get_referer();
    $submitted_data['referrer_uri'] = isset($_SESSION['mtp_first_referrer']) ? $_SESSION['mtp_first_referrer'] : '';
    
    // Remove referrer from session
    unset($_SESSION['mtp_first_referrer']);

    $args = array(
        'body' => json_encode($submitted_data),
        'headers' => array(
            'Content-Type' => 'application/json'
        ),
        'timeout' => 45,
        'sslverify' => false
    );

    wp_remote_post($webhooks[$form_post_id], $args);

}
