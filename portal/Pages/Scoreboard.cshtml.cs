using Microsoft.AspNetCore.Mvc.RazorPages;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using WebHackingPortal.Data;
using WebHackingPortal.Models;
using WebHackingPortal.Services;
using Microsoft.AspNetCore.Http;
using Microsoft.EntityFrameworkCore;

namespace WebHackingPortal.Pages
{
    public class ScoreboardModel : PageModel
    {
        private readonly LabService _labService;
        private readonly AppDbContext _db;

        public List<Lab> Labs { get; set; } = new();
        public Dictionary<string, bool> SolvedChallenges { get; set; } = new();
        public int TotalPoints => Labs.Sum(lab => lab.Challenges.Sum(c => c.Points));
        public int SolvedPoints => Labs
            .Sum(lab => lab.Challenges
                .Where(c => SolvedChallenges.GetValueOrDefault($"lab{lab.Id}-challenge{c.Id}", false))
                .Sum(c => c.Points));
        public ScoreboardModel(LabService labService, AppDbContext db)
        {
            _labService = labService;
            _db = db;
        }

        public async Task OnGetAsync()
        {
            var userIdStr = HttpContext.Session.GetString("userId");
            if (!int.TryParse(userIdStr, out var userId)) return;

            // Load all labs from JSON
            Labs = _labService.GetAllLabs().ToList();

            // Load solved challenges for current user
            var solved = await _db.Submissions
                .Where(s => s.UserId == userId && s.IsCorrect)
                .Select(s => $"lab{s.LabId}-challenge{s.ChallengeId}")
                .ToListAsync();

            SolvedChallenges = Labs
                .SelectMany(lab => lab.Challenges.Select(c => $"lab{lab.Id}-challenge{c.Id}"))
                .ToDictionary(id => id, id => solved.Contains(id));
        }
    }
}