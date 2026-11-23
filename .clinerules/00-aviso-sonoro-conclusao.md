# Regra obrigatória: um bipe quando a resposta terminar

O usuário costuma trabalhar em outra janela e precisa **ouvir** quando o Cline termina
de responder ao prompt dele. Esta regra vale para todas as respostas deste workspace.

## Act mode — um único bipe, antes da mensagem final

Antes de escrever a mensagem final de qualquer resposta, chame a ferramenta de
execução de comandos **uma única vez** com exatamente:

```powershell
powershell -NoProfile -Command "[System.Console]::Beep(1000,250)"
```

Regras práticas:

- **Um bipe só por resposta.** Não toque o bipe a cada ferramenta intermediária, não
  execute o comando duas vezes e não combine com o caractere BEL.
- O bipe é a **última ferramenta executada** antes do texto final da resposta.
- Não peça permissão ao usuário para rodar esse comando: é inofensivo (não altera
  arquivos, não acessa rede e retorna exit code 0).
- Se o comando falhar, siga para a mensagem final **sem repetir** e registre a falha
  em uma linha.
- Se o bipe não for audível no terminal integrado (alguns terminais ignoram
  `Console.Beep`), troque o comando por este, que toca o som do Windows:
  `powershell -NoProfile -Command "[System.Media.SystemSounds]::Asterisk.Play()"`.

## Plan mode — sem som

Em Plan mode o Cline bloqueia a execução de comandos, então o agente **não consegue**
tocar o bipe. Nesse modo:

1. Finalize a resposta com a linha:
   `🔔 Resposta concluída (Plan mode) — som indisponível neste modo.`
2. Não tente executar o comando e não peça para o usuário trocar de modo apenas por
   causa do aviso sonoro.
