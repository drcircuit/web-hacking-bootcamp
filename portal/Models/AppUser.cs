using System.ComponentModel.DataAnnotations;
using System.Collections.Generic;

namespace WebHackingPortal.Models
{
    public class AppUser
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [StringLength(50)]
        public string Username { get; set; } = string.Empty;
        
        public ICollection<Submission> Submissions { get; set; } = new List<Submission>();
    }
}