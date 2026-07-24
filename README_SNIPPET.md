How to use .env.example and rotate secrets

Summary
- Copy .env.example to .env and fill in real secret values only on your local machine or secured deployment environment.
- Never commit .env to source control. Ensure .env is listed in .gitignore.

Steps
1) Create local .env from the example
   cp .env.example .env
   Edit .env and replace placeholders (REDACTED_...) with real secrets.

2) Do NOT commit .env
   git status
   Ensure .env is untracked. If .env is tracked, run:
     git rm --cached .env
     echo ".env" >> .gitignore
     git add .gitignore
     git commit -m "Ignore local .env"

3) Temporary debugging with AI_DEBUG
   - Only set AI_DEBUG=1 for short local troubleshooting.
   - Ensure AI_DEBUG is set back to 0 and delete logs before pushing any commits.
   - Debug log path defaults to logs/ai_debug.log; include logs/ and ai_debug.log in .gitignore.

4) Key rotation quick checklist (examples)
   - GOOGLE_AI_API_KEY: Revoke and recreate in Google AI Studio (https://aistudio.google.com/apikey)
   - GOOGLE_CLIENT_SECRET: Revoke and recreate in Google Cloud Console (APIs & Services → Credentials)
   - APP_SECRET / DB_PASS: Rotate in your hosting provider or database console

5) After rotation, update local .env and restart services as needed.

6) Do not store keys in CI directly. Use GitHub Actions secrets or cloud provider secret managers.

Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>