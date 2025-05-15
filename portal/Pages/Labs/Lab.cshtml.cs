using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using WebHackingPortal.Data;
using WebHackingPortal.Models;
using WebHackingPortal.Services;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using Microsoft.AspNetCore.Http;

namespace WebHackingPortal.Pages.Labs.Lab
{
    public class IndexModel : PageModel
    {
        private readonly LabService _labService;
        private readonly AppDbContext _db;
        private readonly ILogger<IndexModel> _logger;

        public WebHackingPortal.Models.Lab? Lab { get; set; }

        // Track solved challenges per (LabId, ChallengeId)
        public HashSet<(int LabId, int ChallengeId)> SolvedChallenges { get; set; } = new();
        public Dictionary<int, int> ChallengeAttempts { get; set; } = new();

        public int TotalChallenges => Lab?.Challenges.Count ?? 0;
        public int SolvedCount => SolvedChallenges.Count;

        public IndexModel(LabService labService, AppDbContext db, ILogger<IndexModel> logger)
        {
            _labService = labService;
            _db = db;
            _logger = logger;
        }

        public async Task OnGetAsync(int id)
        {
            var userIdStr = HttpContext.Session.GetString("userId");
            if (!int.TryParse(userIdStr, out var userId))
                return;

            Lab = _labService.GetLabById(id.ToString());

            if (Lab == null) return;

            // Load solved challenges (only correct submissions)
            SolvedChallenges = new HashSet<(int, int)>(
                (await _db.Submissions
                    .Where(s => s.UserId == userId && s.LabId == Lab.Id && s.IsCorrect)
                    .Select(s => new { s.LabId, s.ChallengeId })
                    .ToListAsync())
                    .Select(x => (x.LabId, x.ChallengeId)));

            // Load attempt counts per challenge
            ChallengeAttempts = await _db.Submissions
                .Where(s => s.UserId == userId && s.LabId == Lab.Id)
                .GroupBy(s => s.ChallengeId)
                .ToDictionaryAsync(g => g.Key, g => g.Count());
        }

        public async Task<IActionResult> OnPostAsync(int id, int? challengeId, string? flag)
        {
            var userIdStr = HttpContext.Session.GetString("userId");
            if (!int.TryParse(userIdStr, out var userId))
                return RedirectToPage("/Login");

            Lab = _labService.GetLabById(id.ToString());
            if (Lab == null) return NotFound();

            if (challengeId.HasValue && !string.IsNullOrWhiteSpace(flag))
            {
                var challenge = Lab.Challenges.FirstOrDefault(c => c.Id == challengeId.Value);
                if (challenge != null)
                {
                    // Check if already solved this challenge
                    var alreadySolved = await _db.Submissions.AnyAsync(
                        s => s.UserId == userId &&
                             s.LabId == Lab.Id &&
                             s.ChallengeId == challengeId.Value &&
                             s.IsCorrect);

                    if (!alreadySolved)
                    {
                        var isCorrect = flag.Trim().Equals(challenge.Flag, StringComparison.OrdinalIgnoreCase);

                        var submission = new Submission
                        {
                            UserId = userId,
                            LabId = Lab.Id,
                            ChallengeId = challengeId.Value,
                            SubmittedFlag = flag,
                            IsCorrect = isCorrect,
                            Timestamp = DateTime.UtcNow
                        };

                        _db.Submissions.Add(submission);
                        await _db.SaveChangesAsync();
                    }
                }
            }

            return RedirectToPage("/Labs/Lab", new { id = Lab.Id });
        }
    }
}