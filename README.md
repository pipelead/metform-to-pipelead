# Metform to Pipelead WordPress Plugin

Este plugin permite a integração entre formulários criados com o Metform e o Pipelead, enviando automaticamente os dados submetidos para a plataforma Pipelead via webhook.

## Descrição

O Metform to Pipelead é um plugin de integração que conecta seu formulário Metform diretamente ao Pipelead. Quando um visitante preenche um formulário em seu site, os dados são automaticamente enviados para o Pipelead, incluindo informações importantes como:

- Todos os campos do formulário
- URL da página onde o formulário foi preenchido
- URL de origem do visitante (referrer)

## Requisitos

- WordPress 5.0 ou superior
- Plugin Metform instalado e ativo
- Conta ativa no Pipelead

## Instalação

1. Faça o download do arquivo ZIP mais recente na [página de releases](https://github.com/seu-usuario/metform-to-pipelead/releases)
2. No seu painel WordPress, vá até Plugins > Adicionar Novo > Enviar Plugin
3. Selecione o arquivo ZIP baixado e clique em "Instalar Agora"
4. Após a instalação, clique em "Ativar Plugin"

## Configuração

1. No painel WordPress, acesse o menu "Metform Pipelead"
2. Você verá uma lista de todos os seus formulários Metform
3. Para cada formulário que deseja integrar:
   - Acesse sua conta no Pipelead
   - Vá até a seção de Formulários
   - Selecione ou crie um formulário
   - Copie o endpoint gerado
   - Cole o endpoint no campo correspondente ao formulário no WordPress
4. Clique em "Salvar"

## Desenvolvimento Local

Para desenvolvimento local, o plugin automaticamente desativa a verificação SSL para permitir testes em ambiente de desenvolvimento. **Note que em produção a verificação SSL permanece ativa por questões de segurança.**

## Como Contribuir

Contribuições são sempre bem-vindas! Aqui estão algumas formas de contribuir:

1. Fork o repositório
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Diretrizes para Contribuição

- Mantenha o código limpo e bem documentado
- Siga os padrões de codificação do WordPress
- Teste suas alterações em diferentes ambientes
- Atualize a documentação quando necessário

## Releases

As releases são criadas automaticamente quando uma nova tag é enviada ao repositório. Para criar uma nova release:

1. Atualize a versão no arquivo principal do plugin
2. Crie uma tag: `git tag vX.X.X`
3. Envie a tag: `git push origin vX.X.X`

## Licença

Este projeto está licenciado sob a GPL v2 ou posterior - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte

Para suporte, por favor:
1. Abra uma [issue](https://github.com/seu-usuario/metform-to-pipelead/issues) no GitHub
2. Entre em contato com o suporte do Pipelead para questões relacionadas à plataforma

## Changelog

### 0.1
- Versão inicial do plugin
- Integração básica com webhook
- Captura de URL atual e referenciador

### 0.2
- Atualizador via Github

### 0.3
- Mensagem caso não tenha o Metform instalado