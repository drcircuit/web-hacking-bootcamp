using System.ComponentModel.DataAnnotations;
using System.Collections.Generic;

namespace WebHackingPortal.Models
{
    public class Challenge
    {
        [Key]
        public int Id { get; set; }

        [Required]
        public string Title { get; set; } = string.Empty;

        [Required]
        public string Description { get; set; } = string.Empty;

        [Required]
        public string Category { get; set; } = string.Empty;

        public int Points { get; set; }

        [Required]
        public string Flag { get; set; } = string.Empty;

        public int Order { get; set; }

        public int? LabId { get; set; }
        public Lab? Lab { get; set; }

        // Navigation properties
        public ICollection<Submission> Submissions { get; set; } = new List<Submission>();
    }
}