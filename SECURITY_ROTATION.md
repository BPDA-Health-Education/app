Security rotation & revocation checklist

Summary (auto-generated):
- Found sensitive filenames in repository history and working tree: .ai_debug.log, .env.bak (these are present in recent commits and in the working tree). Verify and purge immediately.

Immediate verification commands (run locally):
- Verify no secret tokens remain in any commit:
  git rev-list --all | xargs -n1 git grep -n --no-index 'AQ.Ab8RN6K14C' || echo "no match"
- List commits that mention the filenames:
  git log --all --name-only --pretty=format:%H | grep -E '\.env.bak|\.ai_debug.log' | sort -u
- Check working tree for sensitive files:
  git ls-files | grep -E '\.env$|\.env.bak$|\.ai_debug\.log$' || echo "none tracked"

Purge sensitive files from history (recommended):
1) Use BFG (simpler) or git filter-repo (preferred for complex policies):
   # BFG example (run from repo root):
   java -jar bfg.jar --delete-files ".env.bak,.ai_debug.log"

2) Finalize cleanup and compress repo:
   git reflog expire --expire=now --all
   git gc --prune=now --aggressive

3) Force-push cleaned history to remote (ALL branches & tags):
   git push --force origin --all
   git push --force origin --tags

4) Remove sensitive working files and commit the removal (do this after push):
   Remove-Item .env.bak,.ai_debug.log -Force
   echo ".env.bak" >> .gitignore
   echo ".ai_debug.log" >> .gitignore
   git add .gitignore
   git commit -m "Remove sensitive artifacts from working tree and ignore them\n\nCo-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"
   git push origin main

Immediate secret rotation (DO THIS NOW):
- GitHub Personal Access Token (PAT):
  1) Revoke the leaked token: https://github.com/settings/tokens
  2) Create a new token with least privileges needed; store it in your password manager.
  3) If you used the token in CI, update GitHub Actions or secrets immediately.

- Google AI / Gemini API key (GOOGLE_AI_API_KEY):
  1) Go to Google AI Studio API keys: https://aistudio.google.com/apikey
  2) Revoke/delete the exposed key.
  3) Create a new key, copy it to your secure vault, and update your local .env (DO NOT commit).
  4) If the key was used in production, rotate any dependent credentials and monitor for misuse.

- Google OAuth client secrets (GOOGLE_CLIENT_SECRET):
  1) Revoke and rotate in Google Cloud Console (APIs & Services → Credentials):
     https://console.cloud.google.com/apis/credentials
  2) Update your app configuration and redirect URIs as needed.

- Other secrets (APP_SECRET, DB_PASS, etc.):
  1) Rotate database passwords, API keys, and any other credentials found in .env.
  2) If passwords were stored in external services (hosting, DB), follow their rotation steps.

Post-rotation checklist:
- Ensure .env and debug logs are present in .gitignore.
- Remove AI_DEBUG or set it to 0 in your local .env and delete any debug logs.
- Revoke the leaked GitHub token and create a new one. Update local git remote URLs to use secure credential helpers (Git Credential Manager/Core) instead of embedding tokens in URLs.
- Scan repository and remote branches/tags for leaked values (run the verification commands again).
- Consider running an audit (GitHub secret scanning if available) and notify any stakeholders about the incident.

Useful links:
- Revoke PAT: https://github.com/settings/tokens
- Repo secrets (Actions): https://github.com/<owner>/<repo>/settings/secrets/actions
- Google AI Studio API keys: https://aistudio.google.com/apikey
- Google Cloud credentials: https://console.cloud.google.com/apis/credentials

If you want, I can now:
- Run more local verification scans for other likely secret patterns (AWS keys, other API prefixes)
- Produce a small PR file (.env.example) with placeholders and a README snippet explaining key rotation (committed locally)
- Prepare exact UI steps (screenshots & click-by-click) for GitHub and Google if you need them.
